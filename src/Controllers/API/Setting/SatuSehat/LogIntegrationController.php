<?php

namespace Projects\WellmedGateway\Controllers\API\Setting\SatuSehat;

use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Setting\SatuSehat\GeneralSetting\{
    ViewRequest, ShowRequest, StoreRequest, DeleteRequest
};

class LogIntegrationController extends ApiController{
    // public function __construct(
    //     protected LogIntegration $__schema
    // ){
    //     parent::__construct();
    // }

    public function index(ViewRequest $request){
        return $this->__schema->viewSatuSehatLogIntegrationList();
    }

    public function show(ShowRequest $request){
        return $this->__schema->showSatuSehatLogIntegration();
    }

    public function store(StoreRequest $request){
        return $this->__schema->storeSatuSehatLogIntegration();
    }

    public function destroy(DeleteRequest $request){
        return $this->__schema->deleteSatuSehatLogIntegration();
    }
}