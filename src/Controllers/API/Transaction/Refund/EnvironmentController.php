<?php

namespace Projects\WellmedGateway\Controllers\API\Transaction\Refund;

use Projects\WellmedGateway\Controllers\API\Transaction\BaseWalletEnvironmentController as BaseEnv;

class EnvironmentController extends BaseEnv{
    protected function getRefundList(?callable $callback = null){        
        $this->commonRequest();
        return $this->__refund_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $query->when(isset($callback),function ($query) use ($callback){
                $callback($query);
            });
        })->viewRefundList();
    }

    protected function getRefundPaginate(?callable $callback = null){        
        $this->commonRequest();
        return $this->__refund_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $query->when(isset($callback),function ($query) use ($callback){
                $callback($query);
            });
        })->viewRefundPaginate();
    }

    protected function showRefund(?callable $callback = null){        
        $this->commonRequest();
        return $this->__refund_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $query->when(isset($callback),function ($query) use ($callback){
                $callback($query);
            });
        })->showRefund();
    }

    protected function deleteRefund(?callable $callback = null){        
        $this->commonRequest();
        return $this->__refund_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $callback($query);
        })->deleteRefund();
    }

    protected function storeRefund(?callable $callback = null){
        $this->commonRequest();
        return $this->__refund_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $callback($query);
        })->storeRefund();
    }
}