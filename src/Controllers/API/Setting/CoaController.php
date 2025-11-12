<?php

namespace Projects\WellmedGateway\Controllers\API\Setting;

use Hanafalah\ModulePayment\Contracts\Schemas\Coa;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Setting\Coa\{
    ViewRequest, StoreRequest, DeleteRequest
};

class CoaController extends ApiController{
    public function __construct(
        protected Coa $__coa_schema
    ){
        parent::__construct();
    }

    public function index(ViewRequest $request){
        return $this->__coa_schema->viewCoaList();
    }

    public function store(StoreRequest $request){
        return $this->__coa_schema->storeCoa();
    }

    public function destroy(DeleteRequest $request){
        return $this->__coa_schema->deleteCoa();
    }
}