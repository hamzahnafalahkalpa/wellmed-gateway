<?php

namespace Projects\WellmedGateway\Controllers\API\SupplyChain\Purchasing;

use Hanafalah\ModuleProcurement\Contracts\Schemas\Purchasing;
use Projects\WellmedGateway\Controllers\API\SupplyChain\ProcurementController;
use Projects\WellmedGateway\Requests\API\SupplyChain\Purchasing\{
    ViewRequest, StoreRequest, UpdateRequest, DeleteRequest, ShowRequest
};

class PurchasingController extends ProcurementController{
    public function __construct(
        protected Purchasing $__purchasing_schema    
    ){}

    public function index(ViewRequest $request){
        return $this->__purchasing_schema->conditionals(function($query){
            $query->when(isset(request()->project_id),function($query){
                $query->whereHas('procurement',function($query){
                    $query->where('warehouse_type','Project')->where('warehouse_id',request()->project_id);
                });
            }); 
        })->viewPurchasingPaginate();
    }

    public function show(ShowRequest $request){
        return $this->__purchasing_schema->showPurchasing();
    }

    public function store(StoreRequest $request){
        $purchase_orders = $this->purchasingProcurementSetup(request()->purchase_orders);
        request()->merge([
            'purchase_orders' => $purchase_orders,
            'approval'        => [
                'note' => null,
                'status' => null,
                'approver' => [
                    'estimator_id' => null,
                    'coo_id' => null,
                    'cto_id' => null,
                    'ceo_id' => null
                ]
            ]
        ]);
        return $this->__purchasing_schema->storePurchasing();
    }

    public function update(UpdateRequest $request){
        $approval = $this->approver();
        request()->merge([
            'approval'        => $approval
        ]);
        return $this->__purchasing_schema->updatePurchasing();
    }

    public function destroy(DeleteRequest $request){
        return $this->__purchasing_schema->deletePurchasing();
    }
}