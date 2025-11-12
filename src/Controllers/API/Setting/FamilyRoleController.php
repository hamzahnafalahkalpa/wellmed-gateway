<?php

namespace Projects\WellmedGateway\Controllers\API\Setting;

use Hanafalah\ModulePeople\Contracts\Schemas\FamilyRole;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Setting\FamilyRole\{
    ViewRequest, StoreRequest, DeleteRequest
};

class FamilyRoleController extends ApiController{
    public function __construct(
        protected FamilyRole $__schema
    ){}

    public function index(ViewRequest $request){
        return $this->__schema->viewFamilyRoleList();
    }

    public function store(StoreRequest $request){
        return $this->__schema->storeFamilyRole();
    }

    public function destroy(DeleteRequest $request){
        return $this->__schema->deleteFamilyRole();
    }
}
