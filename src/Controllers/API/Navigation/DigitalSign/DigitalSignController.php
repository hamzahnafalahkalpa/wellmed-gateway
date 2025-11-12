<?php

namespace Projects\WellmedGateway\Controllers\API\Navigation\DigitalSign;

use Hanafalah\ModuleEmployee\Contracts\Schemas\Employee;
use Hanafalah\ModuleHandwriting\Contracts\Schemas\DigitalSign;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Navigation\DigitalSign\{
    ViewRequest, StoreRequest
};

class DigitalSignController extends ApiController{
    public function __construct(
        protected DigitalSign $__digital_sign_schema
    ){}

    private function localRequest(){
        $this->userAttempt();
        request()->merge([
            'reference_id'   => $this->global_employee->getKey(),
            'reference_type' => $this->global_employee->getMorphClass()
        ]);
    }

    public function index(ViewRequest $request){
        $this->localRequest();
        return $this->__digital_sign_schema->showDigitalSign();
    }

    public function store(StoreRequest $request){
        $this->localRequest();
        return $this->__digital_sign_schema->storeDigitalSign();
    }
}