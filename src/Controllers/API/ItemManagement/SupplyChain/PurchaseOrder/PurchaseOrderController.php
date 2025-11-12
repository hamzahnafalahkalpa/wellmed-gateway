<?php

namespace Projects\WellmedGateway\Controllers\API\ItemManagement\SupplyChain\PurchaseOrder;

use Hanafalah\ModuleProcurement\Contracts\Schemas\PurchaseOrder;
use Illuminate\Http\Request;
use Projects\WellmedGateway\Controllers\API\ItemManagement\SupplyChain\ProcurementController;
use Projects\WellmedGateway\Requests\API\ItemManagement\SupplyChain\PurchaseOrder\{
    ViewRequest, ShowRequest
};

class PurchaseOrderController extends ProcurementController
{
    public function __construct(
        protected PurchaseOrder $__purchase_order
    ){
        parent::__construct();
    }

    public function index(ViewRequest $request){
        return $this->__purchase_order->viewPurchaseOrderPaginate();
    }

    public function show(ShowRequest $request){
        return $this->__purchase_order->showPurchaseOrder();
    }

    public function export(Request $request){
        return $this->__purchase_order->export('PurchaseOrder')->handle();
    }
}