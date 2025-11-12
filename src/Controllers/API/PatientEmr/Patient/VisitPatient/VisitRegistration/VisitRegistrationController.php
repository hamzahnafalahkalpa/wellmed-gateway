<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\Patient\VisitPatient\VisitRegistration;

use Projects\WellmedGateway\Controllers\API\PatientEmr\VisitRegistration\EnvironmentController;
use Projects\WellmedGateway\Requests\API\PatientEmr\Patient\VisitPatient\VisitRegistration\{
    ViewRequest, ShowRequest, StoreRequest, DeleteRequest
};

class VisitRegistrationController extends EnvironmentController
{
    protected function commonRequest(){
        request()->merge([
            'search_visit_type'       => 'VisitPatient',
            'search_visit_patient_id' => request()->visit_patient_id
        ]);
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

    public function destroy(DeleteRequest $request){
        return $this->deleteVisitRegistration();
    }
}
