<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\Patient\OldVisit;

use Projects\WellmedGateway\Controllers\API\PatientEmr\Patient\EnvironmentController;
use Illuminate\Http\Request;

class OldVisitController extends EnvironmentController{
    public function index(Request $request){
        return $this->__old_visit_schema->viewOldVisitPaginate();
    }
}