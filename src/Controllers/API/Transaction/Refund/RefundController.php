<?php

namespace Projects\WellmedGateway\Controllers\API\Transaction\Refund;

use Projects\WellmedGateway\Requests\API\Transaction\Refund\{
    ViewRequest, ShowRequest
};

class RefundController extends EnvironmentController{
    public function index(ViewRequest $request){
        return $this->getRefundPaginate();
    }

    public function show(ShowRequest $request){
        return $this->showRefund();
    }
}