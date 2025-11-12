<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\VisitExamination\Assessment;

use Projects\WellmedGateway\Controllers\API\PatientEmr\VisitExamination\Assessment\EnvironmentController;
use Projects\WellmedGateway\Requests\API\PatientEmr\VisitExamination\Assessment\{
    ViewRequest, StoreRequest, ShowRequest
};

class AssessmentController extends EnvironmentController
{
    public function index(ViewRequest $request){
        return $this->getAssessment();
    }

    public function show(ShowRequest $request){
        return $this->getAssessment();
    }

    public function store(StoreRequest $request){
        return $this->storeAssessment();
    }
}
