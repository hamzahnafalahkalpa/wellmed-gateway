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
        if (isset(request()->medic_service_id)){
            $medic_service = $this->MedicServiceModel()->findOrFail(request()->medic_service_id);
            if (in_array($medic_service->label,config('module-patient.direct_referral_froms'))){
                request()->merge([
                    'status' => 'PROCESS'
                ]);
            }
        }
        $visit_registration = request()->visit_registration ?? [
            'id' => null,
            'medic_service_id' => request()->medic_service_id,
            'practitioner_evaluation' => [
                'practitioner_id' => $this->global_employee->getKey()
            ]
        ];
        $timezone = config('app.client_timezone', 'Asia/Jakarta');
        $today = \Carbon\Carbon::now($timezone)->format('Y-m-d');
        request()->merge([
            'visited_at' => request()->visited_at ?? $today,
            'visit_registration' => $visit_registration
        ]);
        return $this->storeReferral();
    }

    public function destroy(DeleteRequest $request){
        return $this->deleteReferral();
    }
}