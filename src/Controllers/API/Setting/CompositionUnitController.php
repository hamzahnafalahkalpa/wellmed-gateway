<?php

namespace Projects\WellmedGateway\Controllers\API\Setting;

use Hanafalah\ModuleItem\Contracts\Schemas\CompositionUnit;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Setting\CompositionUnit\{
    ViewRequest, StoreRequest, DeleteRequest
};

class CompositionUnitController extends ApiController{
    public function __construct(
        protected CompositionUnit $__schema
    ){
        parent::__construct();
    }

    public function index(ViewRequest $request){
        return $this->__schema->viewCompositionUnitList();
    }

    public function store(StoreRequest $request){
        return $this->__schema->storeCompositionUnit();
    }

    public function destroy(DeleteRequest $request){
        return $this->__schema->deleteCompositionUnit();
    }
}