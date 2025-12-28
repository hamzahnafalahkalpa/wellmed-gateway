<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\LetterQueue;

use Projects\WellmedGateway\Controllers\API\PatientEmr\VisitRegistration\EnvironmentController as EnvEnvironmentController;

class EnvironmentController extends EnvEnvironmentController{
    protected function commonConditional($query){
        $query->where('props->is_has_medical_legal_doc', true)
              ->with(['visitPatient','assessments' => function($query){
                    $query->whereIn('morph',['InformedConsent','MedicalCertificate']);
                }]);
    }
}
