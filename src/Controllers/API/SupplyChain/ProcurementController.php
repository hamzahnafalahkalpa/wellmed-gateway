<?php

namespace Projects\WellmedGateway\Controllers\API\SupplyChain;

use Projects\WellmedGateway\Controllers\API\ApiController;
use Illuminate\Support\Str;

class ProcurementController extends ApiController{
    protected function procurementSetup(? array $procurement = null){
        $this->userAttempt();   
        $procurement['warehouse_type'] ??= 'Room';
        // $has_workspace_id = isset($procurement['warehouse_id']);
        if (!isset($procurement['warehouse_id'])) {
            $procurement['warehouse_id'] = $this->global_employee->room->getKey();
            // $has_workspace_id = isset($procurement['warehouse_id']);
        }

        // if ($has_workspace_id) {
        //     config()->set('module-warehouse.warehouse',$warehouse_type);
        //     config()->set('module-item.warehouse',$warehouse_type);
        // }
        // if (isset($procurement['card_stocks']) && count($procurement['card_stocks']) > 0) {
        //     foreach ($procurement['card_stocks'] as &$card_stock) {
        //         $card_stock['stock_movement']['reference_type'] = $warehouse_type;
        //         $card_stock['stock_movement']['reference_id']   = $procurement['warehouse_id'];
        //     }
        // }
        return $procurement;
    }

    protected function receiveOrderSetup(){
        $attributes     = request()->all();
        $attributes['purchase_order_id'] ??= request()->purchase_order_id;
        if (isset($attributes['form'])){
            $attr_po        = $attributes['form'];
            $attributes['purchase_order_id'] ??= $attr_po['id'];
            $attr_po['procurement'] = $this->procurementSetup($attr_po['procurement']);
        }else{
            $attributes['procurement'] = $this->procurementSetup($attributes['procurement']);
        }
        // $attributes['prop_purchase_order']    = [
        //     'id'           => $attr_po['id'],
        //     'warehouse_id' => $attr_po['warehouse_id'],
        //     'procurement'  => $attr_po['procurement']
        // ];
        // $attributes['procurement']['card_stocks'] ??= [];
        // $ro_card_stocks = &$attributes['procurement']['card_stocks'];
        // foreach ($attributes['procurement']['card_stocks'] as &$card_stock) {
        //     $card_stock_model = $this->CardStockModel()->find($card_stock['id']);
        //     if (isset($attributes['procurement']['id'])){
        //         $card_stock_ro    = $this->CardStockModel()->parentId($card_stock_model->getKey())
        //                                  ->where('reference_type','Procurement')
        //                                  ->where('reference_id',$attributes['procurement']['id'])
        //                                  ->where('item_id',$card_stock_model->item_id)->first();
        //     }
        //     $parent_card_stock_model = $this->CardStockModel()->with(['item','stockMovement'])->find($card_stock['id']);
        //     $card_stock['receive_qty'] = floatval($card_stock['receive_qty']);
        //     $ro_card_stocks[] = [
        //         'id'        => $card_stock_ro->id ?? null,
        //         'parent_id' => $card_stock['id'],
        //         'item_id'   => $card_stock_model->item_id,
        //         'qty'       => $card_stock['receive_qty'],
        //         'stock_movement' => [
        //             'id'        => $card_stock_ro?->stockMovement->id ?? null,
        //             'parent_id' => $card_stock_model->stockMovement->id ?? null,
        //             'cogs'      => floatval($card_stock['cogs'] ?? 0),
        //             'receive_qty' => $card_stock['receive_qty'],
        //         ]
        //     ];

        //     $card_stock['total_receive_qty'] = ($parent_card_stock_model->total_receive_qty ?? 0) + $card_stock['receive_qty'];
        //     $card_stock['item']              = [
        //         'id' => $parent_card_stock_model->item->getKey(),
        //         'name' => $parent_card_stock_model->item->name,
        //         'unit' => $parent_card_stock_model->item->prop_unit
        //     ];
        // }
        request()->replace($attributes);
    }

    protected function purchasingProcurementSetup(array $purchase_orders){

        foreach ($purchase_orders as &$purchase_order) {
            $procurement = &$purchase_order['procurement'];
            $procurement['warehouse_type'] = request()->procurement['warehouse_type'] ?? null;
            $procurement['warehouse_id']   = request()->procurement['warehouse_id'] ?? null;
            if (isset($procurement['rab_work_lists']) && count($procurement['rab_work_lists']) > 0) {
                $procurement['prop_rab_work_lists'] = $procurement['rab_work_lists'];
                unset($procurement['rab_work_lists']);
            }
            if (isset(request()?->procurement['warehouse_id'])){
                $procurement['warehouse_type'] = request()?->procurement['warehouse_type'] ?? 'Room';
                $procurement['warehouse_id']   = request()?->procurement['warehouse_id'];
            }
            if (isset($purchase_order['supplier_id'])){
                $purchase_order['supplier_type'] ??= 'Supplier';
            }
            $procurement = $this->procurementSetup($procurement);
            config()->set('module-warehouse.warehouse',$procurement['warehouse_type']);
            config()->set('module-item.warehouse',$procurement['warehouse_type']);
            $purchase_order['procurement'] = $procurement;
        }
        return $purchase_orders;
    }

    protected function getPurchaseRequestPaginate($schema){
        request()->merge([
            'warehouse_type' => request()?->procurement['warehouse_type'] ?? 'Room'
        ]);
        return $schema->conditionals(function($query){
            $query->when(isset(request()?->procurement['warehouse_id']),function($query){
                $query->whereHas('procurement',function($query){
                    $query->where('warehouse_type',request()?->procurement['warehouse_type'])
                          ->where('warehouse_id',request()?->procurement['warehouse_id']);
                });
            }); 
        })->viewPurchaseRequestPaginate();
    }
}