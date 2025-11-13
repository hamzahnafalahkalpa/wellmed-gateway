<?php

namespace Projects\WellmedGateway\Controllers\API\Setting\SatuSehat;

use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Setting\SatuSehat\PatientIntegration\{
    ViewRequest, ShowRequest, StoreRequest, DeleteRequest
};

class PatientIntegrationController extends ApiController{
    // public function __construct(
    //     protected PatientIntegration $__schema
    // ){
    //     parent::__construct();
    // }

    public function index(ViewRequest $request){
        return $this->__schema->viewSatuSehatPatientIntegrationList();
    }

    public function show(ShowRequest $request){
        return $this->__schema->showSatuSehatPatientIntegration();
    }

    public function store(StoreRequest $request){
        return $this->__schema->storeSatuSehatPatientIntegration();
    }

    public function destroy(DeleteRequest $request){
        return $this->__schema->deleteSatuSehatPatientIntegration();
    }
}