<?php

namespace Projects\WellmedGateway\Controllers\API\Setting;

use Hanafalah\ModuleInformedConsent\Contracts\Schemas\MasterInformedConsent;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Setting\MasterInformedConsent\{
    ViewRequest, StoreRequest, DeleteRequest
};

class MasterInformedConsentController extends ApiController{
    public function __construct(
        protected MasterInformedConsent $__schema
    ){}

    public function index(ViewRequest $request){
        return $this->__schema->viewMasterInformedConsentList();
    }

    public function store(StoreRequest $request){
        $service = request()->service;
        foreach ($service['service_prices'] as &$service_price) {
            $service_price['service_item_type'] ??= 'TariffComponent';
        }
        request()->merge(['service' => $service]);
        return $this->__schema->storeMasterInformedConsent();
    }

    public function destroy(DeleteRequest $request){
        return $this->__schema->deleteMasterInformedConsent();
    }
}
