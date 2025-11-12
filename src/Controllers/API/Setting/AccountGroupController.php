<?php

namespace Projects\WellmedGateway\Controllers\API\Setting;

use Hanafalah\ModulePayment\Contracts\Schemas\AccountGroup;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Setting\AccountGroup\{
    ViewRequest, ShowRequest, StoreRequest, DeleteRequest
};

class AccountGroupController extends ApiController{
    public function __construct(
        protected AccountGroup $__schema
    ){
        parent::__construct();
    }

    public function index(ViewRequest $request){
        return $this->__schema->viewAccountGroupList();
    }

    public function show(ShowRequest $request){
        return $this->__schema->viewAccountGroupList();
    }

    public function store(StoreRequest $request){
        if (isset(request()->coas) && count(request()->coas) > 0){
            $coas = request()->coas;
            foreach ($coas as &$coa) $coa['flag'] = 'Coa';
            request()->merge([
                'coas' => $coas
            ]);
        }
        return $this->__schema->storeAccountGroup();
    }

    public function destroy(DeleteRequest $request){
        return $this->__schema->deleteAccountGroup();
    }
}