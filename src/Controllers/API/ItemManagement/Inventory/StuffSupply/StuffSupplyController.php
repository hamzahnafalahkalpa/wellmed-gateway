<?php

namespace Projects\WellmedGateway\Controllers\API\ItemManagement\Inventory\StuffSupply;

use Hanafalah\ModuleItem\Contracts\Schemas\StuffSupply;
use Hanafalah\ModuleItem\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\ItemManagement\Inventory\StuffSupply\{
    ViewRequest, StoreRequest, DeleteRequest
};

class StuffSupplyController extends ApiController{
    public function __construct(
        protected StuffSupply $__schema
    ){
        parent::__construct();
    }

    public function index(ViewRequest $request){
        return $this->__schema->viewStuffSupplyPaginate();
    }

    public function store(StoreRequest $request){
        return $this->__schema->storeStuffSupply();
    }

    public function destroy(DeleteRequest $request){
        return $this->__schema->deleteStuffSupply();
    }
}