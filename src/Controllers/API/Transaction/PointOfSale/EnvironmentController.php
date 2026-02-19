<?php

namespace Projects\WellmedGateway\Controllers\API\Transaction\PointOfSale;

use Projects\WellmedGateway\Controllers\API\Transaction\EnvironmentController as EnvTransaction;

class EnvironmentController extends EnvTransaction{

    protected function getPosTransactionPaginate(?callable $callback = null){        
        $this->commonRequest();
        return $this->__pos_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $query->when(isset($callback),function ($query) use ($callback){
                $callback($query);
            });
        })->viewPosTransactionPaginate();
    }

    protected function showPosTransaction(?callable $callback = null){        
        $this->commonRequest();
        return $this->__pos_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $query->when(isset($callback),function ($query) use ($callback){
                $callback($query);
            });
        })->showPosTransaction();
    }

    protected function deletePosTransaction(?callable $callback = null){        
        $this->commonRequest();
        return $this->__pos_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $callback($query);
        })->deletePosTransaction();
    }

    protected function storePosTransaction(?callable $callback = null){
        $this->commonRequest();
        return $this->__pos_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $callback($query);
        })->storePosTransaction();
    }
}