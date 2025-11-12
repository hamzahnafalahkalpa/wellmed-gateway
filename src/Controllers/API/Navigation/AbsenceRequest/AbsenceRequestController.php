<?php

namespace Projects\WellmedGateway\Controllers\API\Navigation\AbsenceRequest;

use Hanafalah\ModuleEmployee\Contracts\Schemas\AbsenceRequest;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Navigation\AbsenceRequest\{
    StoreRequest
};

class AbsenceRequestController extends ApiController{
    public function __construct(
        protected AbsenceRequest $__absence_request
    ){
        parent::__construct();
    }

    private function localRequest(){
        $this->userAttempt();
        request()->merge([
            'employee_id'   => $this->global_employee->getKey()
        ]);
    }

    public function store(StoreRequest $request){
        $this->localRequest();
        return $this->__absence_request->storeAbsenceRequest();
    }
}