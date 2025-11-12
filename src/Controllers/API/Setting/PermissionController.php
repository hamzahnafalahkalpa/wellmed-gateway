<?php

namespace Projects\WellmedGateway\Controllers\API\Setting;

use Hanafalah\LaravelPermission\Contracts\Schemas\Permission;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Setting\Permission\{
    ViewRequest
};

class PermissionController extends ApiController{
    public function __construct(
        protected Permission $__permission_schema
    ){
        parent::__construct();
    }

    public function index(ViewRequest $request){
        return $this->__permission_schema->viewPermissionList();
    }
}