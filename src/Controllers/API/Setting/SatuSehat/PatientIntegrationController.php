<?php

namespace Projects\WellmedGateway\Controllers\API\Setting\SatuSehat;

use Projects\WellmedBackbone\Contracts\Schemas\SatuSehat\PatientIntegration;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Setting\SatuSehat\PatientIntegration\{
    ViewRequest, ShowRequest, StoreRequest, DeleteRequest
};

class PatientIntegrationController extends ApiController{
    public function __construct(
        protected PatientIntegration $__schema
    ){
        parent::__construct();
    }

    public function index(ViewRequest $request){
        return $this->__schema->viewPatientIntegrationPaginate();
    }

    public function show(ShowRequest $request){
        return $this->__schema->showPatientIntegration();
    }

    public function store(StoreRequest $request){
        return $this->__schema->storePatientIntegration();
    }

    // public function destroy(DeleteRequest $request){
    //     return $this->__schema->deletePatientIntegration();
    // }
}