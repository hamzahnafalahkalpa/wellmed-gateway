<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\Patient\VisitPatient;

use Projects\WellmedGateway\Requests\API\PatientEmr\Patient\VisitPatient\{
    ShowRequest, ViewRequest, DeleteRequest, StoreRequest
};
use Projects\WellmedGateway\Controllers\API\PatientEmr\VisitPatient\EnvironmentController;

class VisitPatientController extends EnvironmentController{

    protected function commonRequest(){
        request()->merge([
            'search_patient_id' => request()->patient_id
        ]);
    }

    public function index(ViewRequest $request){
        return $this->getVisitPatientPaginate();
    }

    public function store(StoreRequest $request){
        return $this->storeVisitPatient();
    }

    public function show(ShowRequest $request){
        return $this->showVisitPatient();
    }

    public function destroy(DeleteRequest $request){
        return $this->deleteVisitPatient();
    }
}
