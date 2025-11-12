<?php

namespace Projects\WellmedGateway\Controllers\API\Setting;

use Hanafalah\ModulePatient\Contracts\Schemas\PatientTypeService;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Setting\PatientTypeService\{
    ViewRequest, StoreRequest, DeleteRequest
};

class PatientTypeServiceController extends ApiController{
    public function __construct(
        protected PatientTypeService $__schema
    ){
        request()->merge(['flag' => 'PatientTypeService']);
        parent::__construct();
    }

    public function index(ViewRequest $request){
        return $this->__schema->viewPatientTypeServiceList();
    }

    public function store(StoreRequest $request){
        return $this->__schema->storePatientTypeService();
    }

    public function destroy(DeleteRequest $request){
        return $this->__schema->deletePatientTypeService();
    }
}