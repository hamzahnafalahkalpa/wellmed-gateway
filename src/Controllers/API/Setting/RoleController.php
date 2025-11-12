<?php

namespace Projects\WellmedGateway\Controllers\API\Setting;

use Hanafalah\LaravelPermission\Contracts\Schemas\Role;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Setting\Role\{
    DeleteRequest, ViewRequest, ShowRequest, StoreRequest
};

class RoleController extends ApiController{
    public function __construct(
        protected Role $__role_schema
    ){
        parent::__construct();
    }

    public function index(ViewRequest $request){
        return $this->__role_schema->viewRoleList();
    }

    public function show(ShowRequest $request){
        return $this->__role_schema->showRole();
    }

    public function store(StoreRequest $request){
        return $this->__role_schema->storeRole();
    }

    public function destroy(DeleteRequest $request){
        return $this->__role_schema->deleteRole();
    }
}