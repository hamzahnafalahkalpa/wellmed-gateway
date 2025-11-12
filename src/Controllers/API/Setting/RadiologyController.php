<?php

namespace Projects\WellmedGateway\Controllers\API\Setting;

use Hanafalah\ModuleLabRadiology\Contracts\Schemas\Radiology;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Setting\Radiology\{
    ViewRequest, StoreRequest, DeleteRequest, ShowRequest
};

class RadiologyController extends ApiController{
    public function __construct(
        protected Radiology $__schema
    ){
        parent::__construct();
    }

    public function index(ViewRequest $request){
        return $this->__schema->viewRadiologyList();
    }

    public function show(ShowRequest $request){
        return $this->__schema->showRadiology();
    }

    public function store(StoreRequest $request){
        $treatment = request()->treatment;
        foreach ($treatment['service_prices'] as &$service_price) {
            $service_price['service_item_type'] ??= 'TariffComponent';
        }

        request()->merge([
            'treatment' => $treatment
        ]);
        return $this->__schema->storeRadiology();
    }

    public function destroy(DeleteRequest $request){
        return $this->__schema->deleteRadiology();
    }
}