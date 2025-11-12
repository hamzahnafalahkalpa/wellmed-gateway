<?php

namespace Projects\WellmedGateway\Controllers\API\Setting;

use Hanafalah\ModuleEvent\Contracts\Schemas\ProgramCategory;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Setting\ProgramCategory\{
    ViewRequest, StoreRequest, DeleteRequest
};

class ProgramCategoryController extends ApiController{
    public function __construct(
        protected ProgramCategory $__schema    
    ){
        parent::__construct();
    }

    public function index(ViewRequest $request){
        return $this->__schema->viewProgramCategoryList();
    }

    public function store(StoreRequest $request){
        return $this->__schema->storeProgramCategory();
    }

    public function destroy(DeleteRequest $request){
        return $this->__schema->deleteProgramCategory();
    }
}