<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\VisitRegistration;

use Projects\WellmedGateway\Controllers\API\PatientEmr\EnvironmentController as EnvEnvironmentController;

class EnvironmentController extends EnvEnvironmentController{
    protected function getVisitRegistrationPaginate(?callable $callback = null){        
        $this->commonRequest();
        return $this->__visit_registration_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $query->where(function($query){
                $query->whereNull('referral_id')
                    ->orWhere(function($query){
                        $query->whereNotNull('referral_id')
                              ->where('props->prop_referral->status','<>','CREATED');
                    });
            })->when(isset($callback),function ($query) use ($callback){
                $callback($query);
            });
        })->viewVisitRegistrationPaginate();
    }

    protected function showVisitRegistration(?callable $callback = null){        
        $this->commonRequest();
        return $this->__visit_registration_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $query->when(isset($callback),function ($query) use ($callback){
                $callback($query);
            });
        })->showVisitRegistration();
    }

    protected function deleteVisitRegistration(?callable $callback = null){        
        $this->commonRequest();
        return $this->__visit_registration_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $callback($query);
        })->deleteVisitRegistration();
    }

    protected function storeVisitRegistration(?callable $callback = null){
        $this->commonRequest();
        if (isset($this->global_room)){
            request()->merge([
                'warehouse_type' => $this->global_room->getMorphClass(),
                'warehouse_id' => $this->global_room->getKey()
            ]);
        }
        return $this->__visit_registration_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $callback($query);
        })->storeVisitRegistration();
    }

    protected function commonRequest(){
        parent::commonRequest();
        if (isset(request()->medic_service_label)){
            $medic_service = $this->MedicServiceModel()->where('label',request()->medic_service_label)->firstOrFail();
            request()->merge([
                'medic_service_id' => $medic_service->id,
            ]);
        }
    }
}
