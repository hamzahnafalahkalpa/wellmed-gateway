<?php

namespace Projects\WellmedGateway\Controllers\API\ItemManagement\SupplyChain\PurchaseRequest;

use Hanafalah\ModuleProcurement\Contracts\Schemas\PurchaseRequest;
use Projects\WellmedGateway\Requests\API\ItemManagement\SupplyChain\PurchaseRequest\{
    ViewRequest, ShowRequest, StoreRequest, DeleteRequest
};
use Projects\WellmedGateway\Controllers\API\ItemManagement\SupplyChain\ProcurementController;

class PurchaseRequestController extends ProcurementController
{
    public function __construct(
        protected PurchaseRequest $__schema
    ){
        parent::__construct();
    }

    public function index(ViewRequest $request){
        return $this->getPurchaseRequestPaginate($this->__schema);
    }

    public function show(ShowRequest $request){
        return $this->__schema->showPurchaseRequest();
    }

    public function store(StoreRequest $request){
        $procurement = $this->procurementSetup(request()->procurement);
        request()->merge(['procurement' => $procurement]);
        return $this->__schema->storePurchaseRequest();
    }

    public function destroy(DeleteRequest $request){
        return $this->__schema->deletePurchaseRequest();
    }
}