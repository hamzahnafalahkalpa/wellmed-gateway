<?php

namespace Projects\WellmedGateway\Controllers\API\SatuSehat\Location;

use Hanafalah\SatuSehat\Contracts\Schemas\Fhir\Location\LocationSatuSehat;
use Illuminate\Http\Request;
use Projects\WellmedGateway\Controllers\API\ApiController;

class LocationController extends ApiController{
    public function __construct(
        protected LocationSatuSehat $__location_schema
    ){
        parent::__construct();
    }

    public function store(Request $request){
        request()->replace([
            'form' => request()->all()
        ]);
        return $this->__location_schema->storeLocationSatuSehat();
    }

    public function update(Request $request){
        return $this->__location_schema->updateLocationSatuSehat();
    }
}