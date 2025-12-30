<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\Reservation;

use Projects\WellmedGateway\Controllers\API\PatientEmr\EnvironmentController as EnvEnvironmentController;

class EnvironmentController extends EnvEnvironmentController{
    protected function getReservationPaginate(?callable $callback = null){        
        $this->commonRequest();
        return $this->__reservation_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $query->when(isset($callback),function ($query) use ($callback){
                $callback($query);
            });
        })->viewReservationPaginate();
    }

    protected function showReservation(?callable $callback = null){        
        $this->commonRequest();
        return $this->__reservation_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $query->when(isset($callback),function ($query) use ($callback){
                $callback($query);
            });
        })->showReservation();
    }

    protected function deleteReservation(?callable $callback = null){        
        $this->commonRequest();
        return $this->__reservation_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $callback($query);
        })->deleteReservation();
    }

    protected function storeReservation(?callable $callback = null){
        $this->commonRequest();
        return $this->__reservation_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $callback($query);
        })->storeReservation();
    }
}
