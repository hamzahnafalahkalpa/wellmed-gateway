<?php

namespace Projects\WellmedGateway\Controllers\API\ItemManagement\Inventory\OfficeSupply;

use Hanafalah\ModuleItem\Contracts\Schemas\OfficeSupply;
use Hanafalah\ModuleItem\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\ItemManagement\Inventory\OfficeSupply\{
    ViewRequest, StoreRequest, DeleteRequest
};

class OfficeSupplyController extends ApiController{
    public function __construct(
        protected OfficeSupply $__schema
    ){
        parent::__construct();
    }

    public function index(ViewRequest $request){
        return $this->__schema->viewOfficeSupplyPaginate();
    }

    public function store(StoreRequest $request){
        return $this->__schema->storeOfficeSupply();
    }

    public function destroy(DeleteRequest $request){
        return $this->__schema->deleteOfficeSupply();
    }
}