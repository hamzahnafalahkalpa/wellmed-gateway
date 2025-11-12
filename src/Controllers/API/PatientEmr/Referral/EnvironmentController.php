<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\Referral;

use Projects\WellmedGateway\Controllers\API\PatientEmr\EnvironmentController as EnvEnvironmentController;

class EnvironmentController extends EnvEnvironmentController{
    protected function getReferralPaginate(?callable $callback = null){        
        $this->commonRequest();
        return $this->__referral_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $query->when(isset($callback),function ($query) use ($callback){
                $callback($query);
            });
        })->viewReferralPaginate();
    }

    protected function showReferral(?callable $callback = null){        
        $this->commonRequest();
        return $this->__referral_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $query->when(isset($callback),function ($query) use ($callback){
                $callback($query);
            });
        })->showReferral();
    }

    protected function deleteReferral(?callable $callback = null){        
        $this->commonRequest();
        return $this->__referral_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $callback($query);
        })->deleteReferral();
    }

    protected function storeReferral(?callable $callback = null){
        $this->commonRequest();
        return $this->__referral_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $callback($query);
        })->storeReferral();
    }
}
