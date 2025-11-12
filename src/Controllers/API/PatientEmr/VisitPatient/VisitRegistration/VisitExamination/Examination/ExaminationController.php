<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\VisitPatient\VisitRegistration\VisitExamination\Examination;

use Projects\WellmedGateway\Requests\API\PatientEmr\Patient\VisitPatient\VisitRegistration\VisitExamination\Examination\{
    StoreRequest, UpdateRequest
};
use Projects\WellmedGateway\Controllers\API\PatientEmr\VisitExamination\Examination\EnvironmentController;

class ExaminationController extends EnvironmentController
{
    public function store(StoreRequest $request){
        return $this->storeExamination();
    }
}
