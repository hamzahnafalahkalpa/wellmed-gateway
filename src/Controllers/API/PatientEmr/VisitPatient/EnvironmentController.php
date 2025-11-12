<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\VisitPatient;

use Projects\WellmedGateway\Controllers\API\PatientEmr\EnvironmentController as EnvEnvironmentController;

class EnvironmentController extends EnvEnvironmentController{
    protected function getVisitPatientPaginate(?callable $callback = null){        
        $this->commonRequest();
        return $this->__visit_patient_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $query->when(isset($callback),function ($query) use ($callback){
                $callback($query);
            });
        })->viewVisitPatientPaginate();
    }

    protected function showVisitPatient(?callable $callback = null){        
        $this->commonRequest();
        return $this->__visit_patient_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $query->when(isset($callback),function ($query) use ($callback){
                $callback($query);
            });
        })->showVisitPatient();
    }

    protected function deleteVisitPatient(?callable $callback = null){        
        $this->commonRequest();
        return $this->__visit_patient_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $callback($query);
        })->deleteVisitPatient();
    }

    protected function storeVisitPatient(?callable $callback = null){
        $this->commonRequest();
        return $this->__visit_patient_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $callback($query);
        })->storeVisitPatient();
    }
}
