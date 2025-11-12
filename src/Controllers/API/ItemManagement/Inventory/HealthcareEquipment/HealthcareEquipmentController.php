<?php

namespace Projects\WellmedGateway\Controllers\API\ItemManagement\Inventory\HealthcareEquipment;

use Hanafalah\ModuleMedicalItem\Contracts\Schemas\HealthcareEquipment;
use Hanafalah\ModuleItem\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\ItemManagement\Inventory\HealthcareEquipment\{
    ViewRequest, StoreRequest, DeleteRequest
};

class HealthcareEquipmentController extends ApiController{
    public function __construct(
        protected HealthcareEquipment $__schema
    ){
        parent::__construct();
    }

    public function index(ViewRequest $request){
        return $this->__schema->viewHealthcareEquipmentPaginate();
    }

    public function store(StoreRequest $request){
        return $this->__schema->storeHealthcareEquipment();
    }

    public function destroy(DeleteRequest $request){
        return $this->__schema->deleteHealthcareEquipment();
    }
}