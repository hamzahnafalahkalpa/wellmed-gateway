<?php

namespace Projects\WellmedGateway\Controllers\API\Menu;

use Hanafalah\LaravelPermission\Contracts\Schemas\Menu;
use Projects\WellmedGateway\Controllers\API\ApiController;

class MenuController extends ApiController{
    public function __construct(
        protected Menu $__schema_menu
    ){
        parent::__construct();
    }

    public function index(){
        $this->userAttempt();
        request()->merge([
            'role_id' => $this->global_user_reference->prop_role['id'],
            'is_menu' => true
        ]);
        return $this->__schema_menu->conditionals(function($query){
            $query->whereNull('parent_id');
        })->viewMenuList();
    }
}