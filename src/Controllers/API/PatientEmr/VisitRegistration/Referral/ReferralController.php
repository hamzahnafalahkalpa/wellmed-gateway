<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\VisitRegistration\Referral;

use Projects\WellmedGateway\Requests\API\PatientEmr\VisitRegistration\Referral\{
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
        $this->commonRequest();
        $visit_registration = request()->visit_registration ?? [
            'id' => null,
            'medic_service_id' => request()->medic_service_id,
            'practitioner_evaluation' => [
                'practitioner_id' => $this->global_employee->getKey()
            ]
        ];
        request()->merge([
            'visit_registration' => $visit_registration
        ]);
        return $this->storeReferral();
    }

    public function delete(DeleteRequest $request){
        return $this->deleteReferral();
    }
}