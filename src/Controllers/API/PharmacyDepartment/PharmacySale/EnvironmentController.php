<?php

namespace Projects\WellmedGateway\Controllers\API\PharmacyDepartment\PharmacySale;

use Projects\WellmedGateway\Controllers\API\PatientEmr\EnvironmentController as EnvEnvironmentController;

class EnvironmentController extends EnvEnvironmentController{
    protected function commonRequest(){
        request()->merge([
            'search_flag' => 'PharmacySale'
        ]);
    }

    protected function getPharmacySalePaginate(?callable $callback = null){        
        $this->commonRequest();
        return $this->__pharmacy_sale_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $query->when(isset($callback),function ($query) use ($callback){
                $callback($query);
            });
        })->viewPharmacySalePaginate();
    }

    protected function showPharmacySale(?callable $callback = null){        
        $this->commonRequest();
        return $this->__pharmacy_sale_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $query->when(isset($callback),function ($query) use ($callback){
                $callback($query);
            });
        })->showPharmacySale();
    }

    protected function deletePharmacySale(?callable $callback = null){        
        $this->commonRequest();
        return $this->__pharmacy_sale_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $callback($query);
        })->deletePharmacySale();
    }

    protected function storePharmacySale(?callable $callback = null){
        $this->commonRequest();
        $medic_service = $this->MedicServiceModel();
        $medic_service = (!isset(request()->medic_service_id))
            ? $medic_service->where('label','INSTALASI FARMASI')->firstOrFail()
            : $medic_service->findOrFail(request()->medic_service_id);

        $service_cluster = $this->ServiceClusterModel();
        $service_cluster = (!isset(request()->service_cluster_id))
            ? $service_cluster->where('label','LINTAS KLUSTER')->firstOrFail()
            : $service_cluster->findOrFail(request()->service_cluster_id);

        $visit_registration = request()->visit_registration;
        $visit_registration = array_merge($visit_registration,[
            'medic_service_id'   => $medic_service->getKey(),
            'service_cluster_id' => $service_cluster->getKey()
        ]);
        request()->merge([
            'visit_registration' => $visit_registration
        ]);
        return $this->__pharmacy_sale_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $callback($query);
        })->storePharmacySale();
    }
}
