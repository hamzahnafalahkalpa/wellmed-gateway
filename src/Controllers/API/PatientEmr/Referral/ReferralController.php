<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\Referral;

use Projects\WellmedGateway\Requests\API\PatientEmr\Referral\{
    ViewRequest, ShowRequest, StoreRequest, DeleteRequest
};

class ReferralController extends EnvironmentController
{ 
    public function commonRequest(){

    }

    public function index(ViewRequest $request){
        return $this->getReferralPaginate();
    }

    public function show(ShowRequest $request){
        return $this->showReferral();
    }

    public function store(StoreRequest $request){
        request()->merge([
            'visit_type' => 'VisitRegistration'
        ]);
        return $this->storeReferral();
    }

    public function delete(DeleteRequest $request){
        return $this->deleteReferral();
    }
}