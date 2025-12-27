<?php

namespace Projects\WellmedGateway\Controllers\API\Transaction\Billing;

use Projects\WellmedGateway\Requests\API\Transaction\Billing\{
    ViewRequest, ShowRequest
};

class BillingController extends EnvironmentController{
    protected function commonConditional($query){
        parent::commonConditional($query);
        $query->whereNotNull('reported_at');
    }

    public function index(ViewRequest $request){
        return $this->getBillingPaginate();
    }

    public function show(ShowRequest $request){
        return $this->showBilling();
    }

    public function kwitansi(){
        request()->merge([
            'transaction_id' => $this->BillingModel()->findOrFail(request()->billing_id)->has_transaction_id
        ]);
        return parent::kwitansi();
    }
}