<?php

namespace Projects\WellmedGateway\Controllers\API\PharmacyDepartment\Dispense\VisitExamination\Examination;

use Projects\WellmedGateway\Requests\API\PharmacyDepartment\Dispense\VisitExamination\Examination\{
    StoreRequest, UpdateRequest
};
use Projects\WellmedGateway\Controllers\API\PatientEmr\VisitExamination\Examination\EnvironmentController;

class ExaminationController extends EnvironmentController
{
    public function store(StoreRequest $request){
        return $this->storeExamination();
    }
}
