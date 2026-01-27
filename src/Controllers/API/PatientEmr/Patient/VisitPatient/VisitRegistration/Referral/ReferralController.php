<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\Patient\VisitPatient\VisitRegistration\Referral;

use Projects\WellmedGateway\Controllers\API\PatientEmr\VisitRegistration\Referral\EnvironmentController;
use Projects\WellmedGateway\Requests\API\PatientEmr\Patient\VisitPatient\VisitRegistration\Referral\{
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
        request()->merge([
            'visit_type' => 'VisitRegistration',
            'visit_id'   => request()->visit_registration_id
        ]);
        return $this->storeReferral();
    }

    public function destroy(DeleteRequest $request){
        return $this->deleteReferral();
    }
}