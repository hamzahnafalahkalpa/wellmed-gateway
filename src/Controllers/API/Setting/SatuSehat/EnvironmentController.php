<?php

namespace Projects\WellmedGateway\Controllers\API\Setting\SatuSehat;

use Hanafalah\SatuSehat\Contracts\Schemas\SatuSehatLog;
use Projects\WellmedBackbone\Services\SatuSehatDashboardService;
use Projects\WellmedGateway\Controllers\API\ApiController;

class EnvironmentController extends ApiController{
    public function __construct(
        protected SatuSehatLog $__schema,
        protected SatuSehatDashboardService $dashboardService
    ){
        parent::__construct();
    }

    protected function commonConditional($query){

    }

    protected function commonRequest(){
        $this->userAttempt();
    }

    protected function getSatuSehatLogPaginate(?callable $callback = null){        
        $this->commonRequest();
        return $this->__schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);  
            $query->when(isset($callback),function ($query) use ($callback){
                $callback($query);
            });
        })->viewSatuSehatLogPaginate();
    }

    protected function showSatuSehatLog(?callable $callback = null){        
        $this->commonRequest();
        return $this->__schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $query->when(isset($callback),function ($query) use ($callback){
                $callback($query);
            });
        })->showSatuSehatLog();
    }

    protected function deleteSatuSehatLog(?callable $callback = null){        
        $this->commonRequest();
        return $this->__schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $callback($query);
        })->deleteSatuSehatLog();
    }

    protected function storeSatuSehatLog(?callable $callback = null){
        $this->commonRequest();
        $result = $this->__schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $callback($query);
        })->storeSatuSehatLog();
        return $result;
    }
}