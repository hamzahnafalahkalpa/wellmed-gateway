<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\VisitPatient\VisitRegistration\VisitExamination\Assessment;

use Illuminate\Support\Str;
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
        request()->merge([
            'morph'            => Str::studly(request()->morph),
            'examination_type' => 'VisitExamination',
            'examination_id'   => request()->visit_examination_id
        ]);
        return app(config('app.contracts.'.request()->morph))->storeAssessment();    }
}
