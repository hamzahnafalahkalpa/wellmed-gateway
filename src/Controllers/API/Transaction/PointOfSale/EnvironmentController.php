<?php

namespace Projects\WellmedGateway\Controllers\API\Transaction\PointOfSale;

use Projects\WellmedGateway\Controllers\API\Transaction\EnvironmentController as EnvTransaction;
use Projects\WellmedGateway\Jobs\ElasticJob;

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

    protected function elasticBillingIndexing(string $billingId){
        $billing = $this->BillingModel();
        $billing = $billing->with($billing->showUsingRelation())->findOrFail($billingId);
        dispatch(new ElasticJob([
            'type'  => 'BULK',
            'datas' => [
                [
                    'index' => config('app.elasticsearch.indexes.billing.full_name'),
                    'data'  => [
                        json_decode(json_encode($billing->toShowApi()->resolve()),true)
                    ]
                ]
            ]
        ]))
        ->onQueue('elasticsearch')
        ->onConnection('rabbitmq');
    }
}