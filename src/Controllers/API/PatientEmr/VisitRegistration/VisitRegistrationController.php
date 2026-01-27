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
}
