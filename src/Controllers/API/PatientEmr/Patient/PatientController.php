<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\Patient;

use Projects\WellmedGateway\Jobs\ElasticJob;
use Projects\WellmedGateway\Requests\API\PatientEmr\Patient\{
    ShowRequest, ViewRequest, DeleteRequest, StoreRequest
};
use Projects\WellmedGateway\Schemas\Elastic;

class PatientController extends EnvironmentController{

    public function index(ViewRequest $request){
        $this->userAttempt();
        $this->recombineRequest();
        return $this->__patient_schema->viewPatientPaginate();
    }

    public function store(StoreRequest $request){
        $this->userAttempt();
        $possibleTypes = ['people'];
        $reference = null;
        $referenceType = null;

        foreach ($possibleTypes as $type) {
            if (request()->filled($type)) {
                $reference = request()->input($type);
                $referenceType = $type;
                break;
            }
        }

        $data = array_fill_keys($possibleTypes, null);
        if (isset($reference)) $data['reference'] = $reference;
        $data['reference_type'] = $referenceType;
        if (isset(request()->visit_examination)){
            $visit_examination = request()->visit_examination;
            $patient_type_service_id = $visit_examination['patient_type_service_id'] ?? $this->PatientTypeServiceModel()->where('label','UMUM')->firstOrFail()->getKey();
            $medic_service_id        = $visit_examination['medic_service_id'] ?? $this->MedicServiceModel()->where('label','UMUM')->firstOrFail()->getKey();

            $practitioner = [ //nullable, FOR HEAD DOCTOR
                "practitioner_type" => "Employee", //nullable, default from config
                "practitioner_id"=> $this->global_employee->getKey(), //GET FROM AUTOLIST - EMPLOYEE LIST (DOCTOR)
                "as_pic"=> true //nullable, default false, in:true/false
            ];
            if (isset($visit_examination['examination'])){
                $visit_examination['practitioner_evaluations'][] = $practitioner;
            }
            $visit_patient = [
                'id' => null,
                "patient_type_service_id" => $patient_type_service_id,
                'practitioner_evaluations' => [[
                    'practitioner_type' => 'Employee',
                    'practitioner_id'   => $this->global_employee->getKey(),
                    'role_as' => 'ADMITTER'
                ]],
                'visit_registration' => [
                    'id' => null,
                    'status' => 'PROCESSING',
                    "practitioner_evaluation" => $practitioner,
                    "medic_service_id"  => $medic_service_id,
                    'visit_examination' => $visit_examination
                ]
            ];
            request()->merge([
                'visit_examination' => null,
                'visit_patient' => $visit_patient
            ]);
        }

        request()->merge($data);
        $data = request()->all();
        unset($data['visit_examination']);
        request()->replace($data);
        $patient = $this->__patient_schema->storePatient();
        $bulks = [
            [
                'index' => config('app.elasticsearch.indexes.patient.full_name'),
                'data'  => [
                    $patient
                ]
            ]
        ];
        if (isset($patient['visit_examination'])){
            $bulks = array_merge($bulks,$this->elasticForVisitPatient($patient['visit_examination']['visit_patient_id'],true));
        }
        dispatch(new ElasticJob([
            'type'  => 'BULK',
            'datas' => [...$bulks]
        ]))
        ->onQueue('elasticsearch')
        ->onConnection('rabbitmq');
        return $patient;
    }

    public function show(ShowRequest $request){
        $this->userAttempt();
        return $this->__patient_schema->showPatient();
    }

    public function destroy(DeleteRequest $request){
        $this->userAttempt();
        return $this->__patient_schema->deletePatient();
    }
}
