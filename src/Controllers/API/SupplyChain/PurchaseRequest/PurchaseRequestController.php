<?php

namespace Projects\WellmedGateway\Controllers\API\SupplyChain\PurchaseRequest;

use Hanafalah\ModuleProcurement\Contracts\Schemas\PurchaseRequest;
use Projects\WellmedGateway\Controllers\API\SupplyChain\ProcurementController;
use Projects\WellmedGateway\Requests\API\SupplyChain\PurchaseRequest\{
    ViewRequest, StoreRequest, DeleteRequest, ShowRequest
};

class PurchaseRequestController extends ProcurementController{
    public function __construct(
        protected PurchaseRequest $__purchase_request_schema    
    ){
        parent::__construct();
    }

    public function index(ViewRequest $request){
        return $this->getPurchaseRequestPaginate($this->__purchase_request_schema);
    }

    public function show(ShowRequest $request){
        return $this->__purchase_request_schema->showPurchaseRequest();
    }
}