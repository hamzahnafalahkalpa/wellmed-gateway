<?php

namespace Projects\WellmedGateway\Controllers\API\Navigation\Profile;

use Hanafalah\ModuleEmployee\Contracts\Schemas\ProfileEmployee;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Navigation\Profile\{
    ShowRequest, StoreRequest
};

class ProfileController extends ApiController{
    public function __construct(
        protected ProfileEmployee $__employee_schema    
    ){
        parent::__construct();
    }

    public function commontRequest(){
        if (isset(request()->uuid)){
            $user_reference_model = $this->UserReferenceModel()->uuid(request()->uuid)->first();
            request()->replace([
                'id' => $user_reference_model->reference_id
            ]);
        }
    }

    public function store(StoreRequest $request){
        $this->commontRequest();
        return $this->__employee_schema->storeProfile();
    }
    
    public function show(ShowRequest $request){
        $this->commontRequest();
        return $this->__employee_schema->showProfile();
    }
}