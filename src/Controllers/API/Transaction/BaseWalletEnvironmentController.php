<?php

namespace Projects\WellmedGateway\Controllers\API\Transaction;

use Hanafalah\ModulePayment\Contracts\Schemas\Deposit;
use Hanafalah\ModulePayment\Contracts\Schemas\Refund;
use Projects\WellmedGateway\Controllers\API\ApiController;

class BaseWalletEnvironmentController extends ApiController{
    public function __construct(
        public Refund $__refund_schema,
        public Deposit $__deposit_schema,
    ){
        parent::__construct();
    }

    protected function commonConditional($query){

    }

    protected function commonRequest(){
        $this->userAttempt();
        if (isset($this->global_employee)){
            request()->merge([
                'author_type' => $this->global_employee->getMorphClass(),
                'author_id'   => $this->global_employee->getKey()
            ]);
        }
    }
}