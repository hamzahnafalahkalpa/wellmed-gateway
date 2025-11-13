<?php

namespace Projects\WellmedGateway\Controllers\API\Setting\SatuSehat;

use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Setting\SatuSehat\GeneralSetting\{
    ViewRequest, ShowRequest, StoreRequest, DeleteRequest
};

class GeneralSettingController extends ApiController{
    // public function __construct(
    //     protected GeneralSetting $__schema
    // ){
    //     parent::__construct();
    // }

    public function index(ViewRequest $request){
        return $this->__schema->viewSatuSehatGeneralSettingList();
    }

    public function show(ShowRequest $request){
        return $this->__schema->showSatuSehatGeneralSetting();
    }

    public function store(StoreRequest $request){
        return $this->__schema->storeSatuSehatGeneralSetting();
    }

    public function destroy(DeleteRequest $request){
        return $this->__schema->deleteSatuSehatGeneralSetting();
    }
}