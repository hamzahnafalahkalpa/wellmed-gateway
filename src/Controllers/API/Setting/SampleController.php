<?php

namespace Projects\WellmedGateway\Controllers\API\Setting;

use Projects\WellmedGateway\Controllers\API\ApiController;
use Hanafalah\ModuleLabRadiology\Contracts\Schemas\Sample as SchemasSample;
use Projects\WellmedGateway\Requests\API\Setting\Sample\{
    ViewRequest, StoreRequest, DeleteRequest, ShowRequest
};
class SampleController extends ApiController{
    public function __construct(
        protected SchemasSample $__schema
    ){
        parent::__construct();
    }

    public function index(ViewRequest  $request){
        return $this->__schema->viewSampleList();
    }

    public function store(StoreRequest $request){
        return $this->__schema->storeSample();
    }

    public function destroy(DeleteRequest $request){
        return $this->__schema->deleteSample();
    }

    public function show(ShowRequest $request){
        return $this->__schema->showSample();
    }
}