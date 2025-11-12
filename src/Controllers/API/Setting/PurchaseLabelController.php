<?php

namespace Projects\WellmedGateway\Controllers\API\Setting;

use Hanafalah\ModuleProcurement\Contracts\Schemas\PurchaseLabel;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Setting\PurchaseLabel\{
    ViewRequest, StoreRequest, DeleteRequest
};

class PurchaseLabelController extends ApiController{
    public function __construct(
        protected PurchaseLabel $__schema    
    ){}

    public function index(ViewRequest $request){
        return $this->__schema->viewPurchaseLabelList();
    }

    public function store(StoreRequest $request){
        return $this->__schema->storePurchaseLabel();
    }

    public function destroy(DeleteRequest $request){
        return $this->__schema->deletePurchaseLabel();
    }
}