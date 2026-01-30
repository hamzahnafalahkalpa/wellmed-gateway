<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\VisitRegistration;

use Projects\WellmedGateway\Requests\API\PatientEmr\VisitRegistration\{
    ViewRequest, StoreRequest, ShowRequest, DeleteRequest, UpdateRequest
};
use Illuminate\Support\Str;

class VisitRegistrationController extends EnvironmentController
{
    protected function commonRequest(){
        parent::commonRequest();
        $medic_service_label = request()->search_medic_service_label ?? request()->flag ?? null;
        if (!isset(request()->search_created_at)){
            request()->merge([
                'search_created_at' => now()->format('Y-m-d')
            ]);
        }
        if (isset($medic_service_label)) {
            $medic_service_label = $this->mustArray($medic_service_label);
            foreach ($medic_service_label as $key => $label) {
                $medic_service_label[$key] = Str::upper($label);
            }
            request()->merge([
                'search_medic_service_label' => $medic_service_label,
            ]);
        }
    }

    public function index(ViewRequest $request){
        return $this->getVisitRegistrationPaginate();

    }

    public function show(ShowRequest $request){
        return $this->showVisitRegistration();
    }

    public function store(StoreRequest $request){
        return $this->storeVisitRegistration();
    }

    public function update(UpdateRequest $request){
        if (!isset(request()->id)) throw new \Exception('Id is required');
        if (!in_array(request()->type,['DPJP'])) throw new \Exception('Type is not available');
        $visit_registration_model = $this->VisitRegistrationModel()->findOrFail(request()->id);
        return $this->transaction(function() use ($visit_registration_model){
            switch (request()->type) {
                case 'DPJP':
                    if (!isset(request()->practitioner_id)) throw new \Exception('Practitioner id is required');
                    $pracitioner_evaluation = $visit_registration_model->practitionerEvaluation;
                    $pracitioner_evaluation->delete();
                    
                    $employee = $this->EmployeeModel()->findOrFail(request()->practitioner_id);
                    app(config('app.contracts.PractitionerEvaluation'))->prepareStorePractitionerEvaluation($this->requestDTO(
                        config('app.contracts.PractitionerEvaluationData'),[
                            'reference_type' => $visit_registration_model->getMorphClass(),
                            'reference_id' => $visit_registration_model->getKey(),
                            'practitioner_type' => 'Employee',
                            'practitioner_type' => request()->practitioner_id,
                            'practitioner_model' => $employee,
                            'profession_id' => $employee->profession_id,
                            'as_pic' => true,
                            'is_commit' => false
                        ]
                    ));
                    $visit_registration_model->load('practitionerEvaluation');
                break;
            }
            return $this->__visit_registration_schema->showVisitRegistration($visit_registration_model);
        });
    }

    public function destroy(DeleteRequest $request){
        return $this->deleteVisitRegistration();
    }

    /**
     * Request EMR export for a visit registration.
     *
     * @param string $id Visit registration ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function export(string $id)
    {
        // Load visit registration
        $visitRegistration = $this->__visit_registration_schema
            ->conditionals(function($query) use ($id) {
                $query->where('id', $id);
            })
            ->showVisitRegistration();

        if (!$visitRegistration) {
            return response()->json([
                'message' => 'Visit registration not found'
            ], 404);
        }

        // Store the visit registration model in the schema
        $this->__visit_registration_schema->entityData($visitRegistration);

        // Call export via DataManagement trait
        $export = $this->__visit_registration_schema->export('VisitRegistrationEmr')->handle();

        return response()->json([
            'message' => 'Export request submitted successfully',
            'export' => [
                'id' => $export->id,
                'status' => $export->status->value,
                'export_type' => $export->export_type,
                'created_at' => $export->created_at,
            ]
        ], 202); // 202 Accepted
    }

    /**
     * Check export status.
     *
     * @param string $exportId Export ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function exportStatus(string $exportId)
    {
        $export = \Hanafalah\LaravelSupport\Models\Export\Export::where('id', $exportId)
            ->where('tenant_id', tenancy()->tenant->id)
            ->firstOrFail();

        $response = [
            'id' => $export->id,
            'status' => $export->status->value,
            'export_type' => $export->export_type,
            'created_at' => $export->created_at,
            'updated_at' => $export->updated_at,
        ];

        if ($export->isCompleted() && $export->canDownload()) {
            $response['download_url'] = route('patient-emr.exports.download', $exportId);
            $response['file_name'] = $export->file_name;
        }

        if ($export->isFailed()) {
            $response['error_message'] = $export->error_message;
        }

        return response()->json($response);
    }

    /**
     * Download export file.
     *
     * @param string $exportId Export ID
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function exportDownload(string $exportId)
    {
        $export = \Hanafalah\LaravelSupport\Models\Export\Export::where('id', $exportId)
            ->where('tenant_id', tenancy()->tenant->id)
            ->firstOrFail();

        if (!$export->isCompleted()) {
            return response()->json([
                'message' => 'Export is not completed yet',
                'status' => $export->status->value
            ], 400);
        }

        if (!$export->canDownload()) {
            return response()->json([
                'message' => 'Export file not found or has been deleted'
            ], 404);
        }

        return response()->download(
            $export->getFullStoragePath(),
            $export->file_name,
            ['Content-Type' => 'application/pdf']
        );
    }
}
