<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\DocumentType\Export;

use Hanafalah\ModuleExamination\Contracts\Schemas\Examination\Assessment\Assessment;
use Projects\WellmedGateway\Controllers\API\ApiController as ApiBaseController;

class EnvironmentController extends ApiBaseController{
    public function __construct(
        protected Assessment $__assessment_schema
    ){
        parent::__construct();
    }

    protected function commonConditional($query){
    }
}