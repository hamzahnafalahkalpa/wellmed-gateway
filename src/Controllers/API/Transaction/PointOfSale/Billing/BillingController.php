<?php

namespace Projects\WellmedGateway\Controllers\API\Transaction\PointOfSale\Billing;

use Projects\WellmedGateway\Controllers\API\Transaction\Billing\EnvironmentController;
use Projects\WellmedGateway\Requests\API\Transaction\PointOfSale\Billing\{
    ViewRequest, ShowRequest, StoreRequest, DeleteRequest
};

class BillingController extends EnvironmentController{
    protected function commonRequest()
    {
        parent::commonRequest();
        request()->merge([
            'has_transaction_id' => request()->transaction_id,
            'author_type'  => request()->author_type ?? $this->global_employee->getMorphClass(),
            'author_id'    => request()->author_id ?? $this->global_employee->getKey(),
            'cashier_type' => request()->cashier_type ?? $this->global_room?->getMorphClass(),
            'cashier_id'   => request()->cashier_id ?? $this->global_room?->getKey()
        ]);
    }

    protected function commonConditional($query){
        $query->whereNull('reported_at');
    }

    public function index(ViewRequest $request){
        return $this->getBillingList();
    }

    public function show(ShowRequest $request){
        return $this->showBilling();
    }

    public function store(StoreRequest $request){
        return $this->storeBilling();
    }

    public function destroy(DeleteRequest $request){
        return $this->deleteBilling();
    }
}