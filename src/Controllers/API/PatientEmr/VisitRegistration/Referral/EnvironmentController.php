<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\VisitRegistration\Referral;

use Projects\WellmedGateway\Controllers\API\PatientEmr\Referral\EnvironmentController as EnvEnvironmentController;

class EnvironmentController extends EnvEnvironmentController{
    protected function commonRequest(){
        request()->merge([
            'search_visit_type' => 'VisitRegistration',
            'search_visit_id'   => request()->visit_registration_id,
            'visit_type'        => 'VisitRegistration',
            'visit_id'          => request()->visit_registration_id
        ]);
        parent::commonRequest();
    }   
}
