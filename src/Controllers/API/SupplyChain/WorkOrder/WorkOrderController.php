<?php

namespace Projects\WellmedGateway\Controllers\API\SupplyChain\WorkOrder;

use Hanafalah\ModuleProcurement\Contracts\Schemas\WorkOrder;
use Projects\WellmedGateway\Controllers\API\SupplyChain\ProcurementController;
use Projects\WellmedGateway\Requests\API\SupplyChain\WorkOrder\{
    ViewRequest, StoreRequest, DeleteRequest, ShowRequest
};

class WorkOrderController extends ProcurementController{
    public function __construct(
        protected WorkOrder $__work_order_schema    
    ){}

    public function index(ViewRequest $request){
        return $this->__work_order_schema->conditionals(function($query){
            $query->when(isset(request()->project_id),function($query){
                $query->whereHas('procurement',function($query){
                    $query->where('warehouse_type','Project')->where('warehouse_id',request()->project_id);
                });
            }); 
        })->viewWorkOrderPaginate();
    }

    public function show(ShowRequest $request){
        return $this->__work_order_schema->showWorkOrder();
    }

    public function store(StoreRequest $request){
        $procurement = request()->procurement;
        $procurement['warehouse_type'] = 'Project';
        $procurement['warehouse_id']   = request()->project_id;
        $procurement['total_tax']      = [
            'total' => 0
        ];
        request()->merge([
            'procurement' => $procurement,
            'supplier_type' => 'SubContractor',
            'supplier_id'   => request()->sub_contractor_id
        ]);
        $purchase_orders = $this->purchasingProcurementSetup(request()->purchase_orders);
        request()->merge(['purchase_orders' => $purchase_orders]);
        return $this->__work_order_schema->storeWorkOrder();
    }


    public function destroy(DeleteRequest $request){
        return $this->__work_order_schema->deleteWorkOrder();
    }
}