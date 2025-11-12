<?php

namespace Projects\WellmedGateway\Controllers\API\Setting;

use Hanafalah\ModuleRencanaAnggaran\Contracts\Schemas\Jasa;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Setting\Jasa\{
    ViewRequest, StoreRequest, DeleteRequest
};

class JasaController extends ApiController{
    public function __construct(
        protected Jasa $__jasa_schema
    ){
        parent::__construct();
    }

    public function index(ViewRequest $request){
        return $this->__jasa_schema->viewJasaList();
    }

    public function store(StoreRequest $request){
        return $this->__jasa_schema->storeJasa();
    }

    public function destroy(DeleteRequest $request){
        return $this->__jasa_schema->deleteJasa();
    }
}