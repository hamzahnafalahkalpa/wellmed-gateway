<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\Patient\VisitExamination;

use Projects\WellmedGateway\Requests\API\PatientEmr\Patient\VisitExamination\{
    StoreRequest
};
// use Projects\WellmedGateway\Controllers\API\PatientEmr\VisitExamination\EnvironmentController;
use Projects\WellmedGateway\Controllers\API\PatientEmr\VisitPatient\EnvironmentController;
use Illuminate\Support\Facades\Log;

class VisitExaminationController extends EnvironmentController
{
    public function store(StoreRequest $request){
        $this->commonRequest();
        $visit_examination = request()->all();
        unset($visit_examination['visit_registration']);
        $patient_model = $this->PatientModel()->findOrFail(request()->patient_id);
        $visit_examination['patient_model'] = $patient_model;
        $patient_type_service_id = $visit_examination['patient_type_service_id'] ?? $this->PatientTypeServiceModel()->where('label','UMUM')->firstOrFail()->getKey();
        $medic_service_id        = $visit_examination['medic_service_id'] ?? $this->MedicServiceModel()->where('label','UMUM')->firstOrFail()->getKey();
        // Reserve next queue number from Elasticsearch (without incrementing counter yet)
        $queue_number = null;
        $queueService = null;
        $esEnabled = config('elasticsearch.enabled', false);

        Log::info('[VisitExamination] Starting queue number reservation', [
            'elasticsearch.enabled' => $esEnabled,
            'patient_id' => request()->patient_id,
            'config_value' => config('elasticsearch.enabled'),
            'config_type' => gettype(config('elasticsearch.enabled'))
        ]);

        if ($esEnabled) {
            try {
                Log::debug('[VisitExamination] Attempting to instantiate VisitRegistrationQueueService');
                $queueService = app(\Projects\WellmedBackbone\Services\VisitRegistrationQueueService::class);
                Log::debug('[VisitExamination] Service instantiated successfully');

                Log::debug('[VisitExamination] Calling reserveNextQueueNumber()');
                $queue_number = $queueService->reserveNextQueueNumber();
                Log::debug('[VisitExamination] reserveNextQueueNumber() returned', [
                    'queue_number' => $queue_number,
                    'type' => gettype($queue_number)
                ]);

                Log::info('[VisitExamination] Queue number reserved successfully', [
                    'queue_number' => $queue_number,
                    'patient_id' => request()->patient_id
                ]);
            } catch (\Throwable $e) {
                Log::error('[VisitExamination] FAILED to reserve queue number from ES', [
                    'error_message' => $e->getMessage(),
                    'error_class' => get_class($e),
                    'error_file' => $e->getFile(),
                    'error_line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'queue_number_after_error' => $queue_number,
                    'patient_id' => request()->patient_id
                ]);
            }
        } else {
            Log::warning('[VisitExamination] Elasticsearch is disabled, queue number will be null', [
                'config_elasticsearch_enabled' => config('elasticsearch.enabled'),
                'patient_id' => request()->patient_id
            ]);
        }

        $visit_registration = [
            'id' => null,
            'status' => 'DRAFT',
            'queue_number' => $queue_number,
            "medic_service_id"  => $medic_service_id,
            'visit_examination' => $visit_examination
        ];
        $req_visit_registration = request()->visit_registration;
        if (isset($req_visit_registration)){
            $visit_registration = array_merge($visit_registration,$req_visit_registration);
        }
        $visit_patient = [
            'id' => null,
            'patient_id' => request()->patient_id,
            "patient_type_service_id" => $patient_type_service_id,
            'patient_model' => $patient_model,
            'visit_registration' => $visit_registration,
            'practitioner_evaluation' => [
                "practitioner_type" => "Employee", //nullable, default from config
                "practitioner_id"=> $this->global_employee->getKey()
            ]
            // 'visit_registration' => [
            //     'id' => null,
            //     'status' => 'PROCESSING',
            //     "practitioner_evaluation" => [ //nullable, FOR HEAD DOCTOR
            //         "practitioner_type" => "Employee", //nullable, default from config
            //         "practitioner_id"=> $this->global_employee->getKey(), //GET FROM AUTOLIST - EMPLOYEE LIST (DOCTOR)
            //         "as_pic"=> true //nullable, default false, in:true/false
            //     ],
            //     "medic_service_id"  => $medic_service_id,
            //     'visit_examination' => $visit_examination
            // ]
        ];
        request()->replace($visit_patient);
        try {
            $visit_patient = $this->storeVisitPatient();

            // Confirm queue number in Elasticsearch after successful registration
            if ($queueService && $queue_number) {
                try {
                    $queueService->confirmQueueNumber();
                    Log::info('Queue number confirmed in ES', [
                        'queue_number' => $queue_number,
                        'patient_id' => request()->patient_id
                    ]);
                } catch (\Throwable $e) {
                    Log::warning('Failed to confirm queue number in ES', [
                        'queue_number' => $queue_number,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            } else {
                Log::info('Queue number not confirmed', [
                    'has_queue_service' => $queueService !== null,
                    'queue_number' => $queue_number,
                    'reason' => $queueService === null ? 'Queue service not initialized' : 'Queue number is null'
                ]);
            }

        } catch (\Throwable $th) {
            Log::error('Failed to store visit patient', [
                'error' => $th->getMessage(),
                'reserved_queue_number' => $queue_number,
                'trace' => $th->getTraceAsString()
            ]);
            throw $th;
        }
        return $this->__visit_examination_schema->showVisitExamination($this->VisitExaminationModel()->findOrFail($visit_patient['visit_registrations'][0]['visit_examination']['id']));
    }
}
