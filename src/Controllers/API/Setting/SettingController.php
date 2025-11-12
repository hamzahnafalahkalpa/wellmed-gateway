<?php

namespace Projects\WellmedGateway\Controllers\API\Setting;

use Hanafalah\LaravelPermission\Contracts\Schemas\Menu;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Setting\{
    ViewRequest
};

class SettingController extends ApiController{
    public function __construct(
        protected Menu $__schema
    ){
        parent::__construct();
    }

    public function index(ViewRequest $request){
        $this->userAttempt();
        request()->merge([
            'role_id'      => $this->global_user_reference->prop_role['id'],
            'is_module'    => true
        ]);
        return $this->__schema->conditionals(function($query){
            $query->whereHas('parent',function($query){
                $query->alias('api.setting.index');
            });
        })->viewMenuList();
    }
}