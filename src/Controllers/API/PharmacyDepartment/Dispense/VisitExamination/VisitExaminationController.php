<?php

namespace Projects\WellmedGateway\Controllers\API\PharmacyDepartment\Dispense\VisitExamination;

use Projects\WellmedGateway\Requests\API\PharmacyDepartment\Dispense\VisitExamination\{
    ViewRequest, ShowRequest
};
use Projects\WellmedGateway\Controllers\API\PatientEmr\VisitExamination\EnvironmentController;

class VisitExaminationController extends EnvironmentController
{
    public function index(ViewRequest $request){
        return $this->getVisitExaminationPaginate();
    }

    public function show(ShowRequest $request){
        return $this->showVisitExamination();
    }
}
