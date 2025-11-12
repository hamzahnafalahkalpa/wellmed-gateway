<?php

namespace Projects\WellmedGateway\Controllers\API\ItemManagement\SupplyChain\Purchasing;

use Hanafalah\ModuleProcurement\Contracts\Schemas\Purchasing;
use Projects\WellmedGateway\Controllers\API\ItemManagement\SupplyChain\ProcurementController;
use Projects\WellmedGateway\Requests\API\ItemManagement\SupplyChain\Purchasing\{
    ApprovalRequest, ViewRequest, ShowRequest, StoreRequest, DeleteRequest,
};

class PurchasingController extends ProcurementController
{
    public function __construct(
        protected Purchasing $__schema
    ){}

    public function index(ViewRequest $request){
        return $this->__schema->conditionals(function($query){
            $query->when(isset(request()->room_id),function($query){
                $query->whereHas('procurement',function($query){
                    $query->where('warehouse_type','Room')->where('warehouse_id',request()->room_id);
                });
            }); 
        })->viewPurchasingPaginate();
    }

    public function show(ShowRequest $request){
        return $this->__schema->showPurchasing();
    }

    public function store(StoreRequest $request){
        $procurement = request()->procurement;
        $procurement['warehouse_type'] ??= 'Room';
        $procurement['total_tax']      = [
            'total' => 0
        ];
        request()->merge(['procurement' => $procurement]);
        $purchase_orders = $this->purchasingProcurementSetup(request()->purchase_orders);
        request()->merge([
            'purchase_orders' => $purchase_orders,
            'approval'        => [
                'note' => null,
                'status' => null,
                'approver' => [
                    'finance_id' => null
                ]
            ]
        ]);        
        return $this->__schema->storePurchasing();
    }

    public function approval(ApprovalRequest $request){
        $approval = $this->approver('Purchasing');
        request()->merge([
            'approval' => $approval
        ]);
        return $this->__schema->updatePurchasing();
    }

    public function destroy(DeleteRequest $request){
        return $this->__schema->deletePurchasing();
    }
}