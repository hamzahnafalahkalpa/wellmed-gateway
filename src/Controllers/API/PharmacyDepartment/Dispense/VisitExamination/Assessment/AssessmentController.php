<?php

namespace Projects\WellmedGateway\Controllers\API\PharmacyDepartment\Dispense\VisitExamination\Assessment;

use Illuminate\Support\Str;
use Projects\WellmedGateway\Controllers\API\PatientEmr\VisitExamination\Assessment\EnvironmentController;
use Projects\WellmedGateway\Requests\API\PharmacyDepartment\Dispense\VisitExamination\Assessment\{
    ViewRequest, StoreRequest, ShowRequest
};

class AssessmentController extends EnvironmentController
{
    public function index(ViewRequest $request){
        return $this->getAssessment();
    }

    public function store(StoreRequest $request){
        request()->merge([
            'morph'            => Str::studly(request()->flag),
            'examination_type' => 'VisitExamination',
            'examination_id'   => request()->visit_examination_id
        ]);
        return $this->__assessment_schema->storeAssessment();
    }

    public function show(ShowRequest $request){
        return $this->getAssessment();
    }
}
