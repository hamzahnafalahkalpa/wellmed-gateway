<?php

namespace Projects\WellmedGateway\Controllers\API\Setting\SatuSehat;

use Hanafalah\SatuSehat\Contracts\Schemas\SatuSehatLog;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Setting\SatuSehat\SatuSehatLog\{
    DeleteRequest
};

class SatuSehatLogController extends ApiController{
    public function __construct(
        protected SatuSehatLog $__schema
    ){
        parent::__construct();
    }

    public function destroy(DeleteRequest $request){
        return $this->__schema->deleteSatuSehatLog();
    }
}