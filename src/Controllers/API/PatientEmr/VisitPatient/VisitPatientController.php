<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\VisitPatient;

use Projects\WellmedGateway\Requests\API\PatientEmr\VisitPatient\{
    ShowRequest, ViewRequest, DeleteRequest, StoreRequest
};

class VisitPatientController extends EnvironmentController{

    public function index(ViewRequest $request){
        return $this->getVisitPatientPaginate();
    }

    public function store(StoreRequest $request){
        $this->commonRequest();
        if (isset($this->global_employee)){
            request()->merge([
                'practitioner_evaluation' => [
                    'practitioner_id' => $this->global_employee->getKey(),
                    'role_as' => 'ADMITTER'
                ]
            ]);
        }
        return $this->storeVisitPatient();
    }

    public function show(ShowRequest $request){
        return $this->showVisitPatient();
    }

    public function destroy(DeleteRequest $request){
        return $this->deleteVisitPatient();
    }
}
