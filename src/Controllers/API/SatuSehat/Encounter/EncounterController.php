<?php

namespace Projects\WellmedGateway\Controllers\API\SatuSehat\Encounter;

use Hanafalah\SatuSehat\Contracts\Schemas\Fhir\Encounter\EncounterSatuSehat;
use Illuminate\Http\Request;
use Projects\WellmedGateway\Controllers\API\ApiController;

class EncounterController extends ApiController{
    public function __construct(
        protected EncounterSatuSehat $__encounter_schema
    ){
        parent::__construct();
    }

    public function store(Request $request){
        request()->replace([
            'form' => request()->all()
        ]);
        return $this->__encounter_schema->storeEncounterSatuSehat();
    }

    public function update(Request $request){
        return $this->__encounter_schema->updateEncounterSatuSehat();
    }
}