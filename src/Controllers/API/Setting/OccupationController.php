<?php

namespace Projects\WellmedGateway\Controllers\API\Setting;

use Hanafalah\ModuleProfession\Contracts\Schemas\Occupation;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Setting\Occupation\{
    ViewRequest, StoreRequest, DeleteRequest
};

class OccupationController extends ApiController{
    public function __construct(
        protected Occupation $__occupation_schema    
    ){}

    public function index(ViewRequest $request){
        return $this->__occupation_schema->viewOccupationList();
    }

    public function store(StoreRequest $request){
        return $this->__occupation_schema->storeOccupation();
    }

    public function destroy(DeleteRequest $request){
        return $this->__occupation_schema->deleteOccupation();
    }
}