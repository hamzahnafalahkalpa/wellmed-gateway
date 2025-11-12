<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\VisitPatient\VisitRegistration\Referral;

use Projects\WellmedGateway\Controllers\API\PatientEmr\VisitRegistration\Referral\EnvironmentController;
use Projects\WellmedGateway\Requests\API\PatientEmr\Patient\VisitRegistration\Referral\{
    ViewRequest, ShowRequest, StoreRequest, DeleteRequest
};

class ReferralController extends EnvironmentController
{ 
    public function index(ViewRequest $request){
        return $this->getReferralPaginate();
    }

    public function show(ShowRequest $request){
        return $this->showReferral();
    }

    public function store(StoreRequest $request){
        return $this->storeReferral();
    }

    public function delete(DeleteRequest $request){
        return $this->deleteReferral();
    }
}