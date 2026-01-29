<?php

namespace Projects\WellmedGateway\Controllers\API\PharmacyDepartment\Frontline\Assessment;

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
