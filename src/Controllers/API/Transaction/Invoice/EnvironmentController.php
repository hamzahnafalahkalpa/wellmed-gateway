<?php

namespace Projects\WellmedGateway\Controllers\API\Transaction\Invoice;

use Hanafalah\ModulePayment\Contracts\Schemas\Invoice;
use Projects\WellmedGateway\Controllers\API\ApiController;

class EnvironmentController extends ApiController{
    public function __construct(
        public Invoice $__schema,
    ){
        parent::__construct();
    }

    protected function commonConditional($query){

    }

    protected function commonRequest(){
        $this->userAttempt();
    }

    protected function getInvoiceList(?callable $callback = null){        
        $this->commonRequest();
        return $this->__schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $query->when(isset($callback),function ($query) use ($callback){
                $callback($query);
            });
        })->viewInvoiceList();
    }

    protected function getInvoicePaginate(?callable $callback = null){        
        $this->commonRequest();
        return $this->__schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $query->when(isset($callback),function ($query) use ($callback){
                $callback($query);
            });
        })->viewInvoicePaginate();
    }

    protected function showInvoice(?callable $callback = null){        
        $this->commonRequest();
        return $this->__schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $query->when(isset($callback),function ($query) use ($callback){
                $callback($query);
            });
        })->showInvoice();
    }

    protected function deleteInvoice(?callable $callback = null){        
        $this->commonRequest();
        return $this->__schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $callback($query);
        })->deleteInvoice();
    }

    protected function storeInvoice(?callable $callback = null){
        $this->commonRequest();
        return $this->__schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $callback($query);
        })->storeInvoice();
    }
}