<?php

namespace Projects\WellmedGateway\Controllers\API\Setting;

use Hanafalah\ModuleItem\Contracts\Schemas\SellingForm;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Setting\SellingForm\{
    ViewRequest, StoreRequest, DeleteRequest
};

class SellingFormController extends ApiController{
    public function __construct(
        protected SellingForm $__sellingform_schema
    ){
        parent::__construct();
    }

    public function index(ViewRequest $request){
        return $this->__sellingform_schema->viewSellingFormList();
    }

    public function store(StoreRequest $request){
        return $this->__sellingform_schema->storeSellingForm();
    }

    public function destroy(DeleteRequest $request){
        return $this->__sellingform_schema->deleteSellingForm();
    }
}