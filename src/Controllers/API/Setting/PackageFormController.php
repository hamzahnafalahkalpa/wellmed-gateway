<?php

namespace Projects\WellmedGateway\Controllers\API\Setting;

use Hanafalah\ModuleMedicalItem\Contracts\Schemas\PackageForm;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Setting\PackageForm\{
    ViewRequest, StoreRequest, DeleteRequest
};

class PackageFormController extends ApiController{
    public function __construct(
        protected PackageForm $__packageform_schema
    ){
        parent::__construct();
    }

    public function index(ViewRequest $request){
        return $this->__packageform_schema->viewPackageFormList();
    }

    public function store(StoreRequest $request){
        return $this->__packageform_schema->storePackageForm();
    }

    public function destroy(DeleteRequest $request){
        return $this->__packageform_schema->deletePackageForm();
    }
}