<?php

namespace Projects\WellmedGateway\Controllers\API\Transaction\Invoice;

use Projects\WellmedGateway\Requests\API\Transaction\Invoice\{
    ViewRequest, ShowRequest
};

class InvoiceController extends EnvironmentController{
    protected function commonConditional($query){
        parent::commonConditional($query);
        $query->whereNotNull('reported_at')->where('props->is_deferred',false);
    }

    public function index(ViewRequest $request){
        return $this->getInvoicePaginate();
    }

    public function show(ShowRequest $request){
        return $this->showInvoice();
    }
}