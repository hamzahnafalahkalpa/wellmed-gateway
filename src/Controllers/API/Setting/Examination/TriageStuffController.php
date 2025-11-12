<?php

namespace Projects\WellmedGateway\Controllers\API\Setting\Examination;

use Hanafalah\ModuleExamination\Contracts\Schemas\TriageStuff;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Setting\Examination\TriageStuff\{
    ViewRequest, StoreRequest
};

class TriageStuffController extends ApiController{
    public function __construct(
        protected TriageStuff $__schema
    ){}

    public function index(ViewRequest $request){
        return $this->__schema->viewTriageStuffList();
    }

    public function store(StoreRequest $request){
        return $this->__schema->storeTriageStuff();
    }
}
