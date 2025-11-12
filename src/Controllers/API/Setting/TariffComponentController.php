<?php

namespace Projects\WellmedGateway\Controllers\API\Setting;

use Hanafalah\ModulePayment\Contracts\Schemas\TariffComponent;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Setting\TariffComponent\{
    ViewRequest, ShowRequest, StoreRequest, DeleteRequest
};

class TariffComponentController extends ApiController{
    public function __construct(
        protected TariffComponent $__schema
    ){
        parent::__construct();
    }

    public function index(ViewRequest $request){
        return $this->__schema->viewTariffComponentList();
    }

    public function show(ShowRequest $request){
        return $this->__schema->showTariffComponent();
    }

    public function store(StoreRequest $request){
        return $this->__schema->storeTariffComponent();
    }

    public function destroy(DeleteRequest $request){
        return $this->__schema->deleteTariffComponent();
    }
}