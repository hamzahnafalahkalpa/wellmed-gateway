<?php

namespace Projects\WellmedGateway\Controllers\API\SatuSehat\Organization;

use Hanafalah\SatuSehat\Contracts\Schemas\Fhir\Organization\OrganizationSatuSehat;
use Illuminate\Http\Request;
use Projects\WellmedGateway\Controllers\API\ApiController;

class OrganizationController extends ApiController{
    public function __construct(
        protected OrganizationSatuSehat $__organization_schema
    ){
        parent::__construct();
    }

    public function store(Request $request){
        request()->replace([
            'form' => request()->all()
        ]);
        return $this->__organization_schema->storeOrganizationSatuSehat();
    }

    public function update(Request $request){
        return $this->__organization_schema->updateOrganizationSatuSehat();
    }
}