<?php

namespace Projects\WellmedGateway\Controllers\API\SatuSehat\Observation;

use Hanafalah\SatuSehat\Contracts\Schemas\Fhir\Observation\ObservationSatuSehat;
use Illuminate\Http\Request;
use Projects\WellmedGateway\Controllers\API\ApiController;

class ObservationController extends ApiController{
    public function __construct(
        protected ObservationSatuSehat $__schema
    ){
        parent::__construct();
    }

    public function store(Request $request){
        request()->replace([
            'form' => request()->all()
        ]);
        return $this->__schema->storeObservationSatuSehat();
    }

    public function update(Request $request){
        return $this->__schema->updateObservationSatuSehat();
    }
}