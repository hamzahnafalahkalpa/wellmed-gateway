<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\Patient\VisitRegistration;

use Projects\WellmedGateway\Controllers\API\PatientEmr\VisitRegistration\EnvironmentController;
use Projects\WellmedGateway\Requests\API\PatientEmr\Patient\VisitRegistration\{
    ViewRequest, ShowRequest, StoreRequest, DeleteRequest
};

class VisitRegistrationController extends EnvironmentController
{
    protected function commonConditional($query){
        $query->where('prop_visit_patient.patient_id',request()->patient_id);
    }

    public function index(ViewRequest $request){
        return $this->getVisitRegistrationPaginate(function($query){
            $query->when(isset(request()->is_unsigned_visits),function($query){
                $query->whereHas('visitExamination',function($query){
                    $query->whereNull('sign_off_at');
                });
            })->when(isset(request()->is_incomplete_diagnosis),function($query){
                $query->whereHas('visitExamination.',function($query){
                    $query->whereNull('sign_off_at');
                });
            });
        });
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
