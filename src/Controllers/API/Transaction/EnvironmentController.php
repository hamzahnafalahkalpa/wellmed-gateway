<?php

namespace Projects\WellmedGateway\Controllers\API\Transaction;

use Hanafalah\ModulePayment\Contracts\Schemas\PosTransaction;
use Hanafalah\ModuleTransaction\Contracts\Schemas\{
    Transaction
};
use Projects\WellmedGateway\Controllers\API\ApiController;

class EnvironmentController extends ApiController{
    public function __construct(
        public Transaction $__transaction_schema,
        public PosTransaction $__pos_schema
    ){
        parent::__construct();
    }

    protected function commonConditional($query){

    }

    protected function commonRequest(){
        $this->userAttempt();
    }

    protected function getTransactionPaginate(?callable $callback = null){        
        $this->commonRequest();
        return $this->__transaction_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $query->when(isset($callback),function ($query) use ($callback){
                $callback($query);
            });
        })->viewTransactionPaginate();
    }

    protected function showTransaction(?callable $callback = null){        
        $this->commonRequest();
        return $this->__transaction_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $query->when(isset($callback),function ($query) use ($callback){
                $callback($query);
            });
        })->showTransaction();
    }

    protected function deleteTransaction(?callable $callback = null){        
        $this->commonRequest();
        return $this->__transaction_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $callback($query);
        })->deleteTransaction();
    }

    protected function storeTransaction(?callable $callback = null){
        $this->commonRequest();
        return $this->__transaction_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $callback($query);
        })->storeTransaction();
    }
}