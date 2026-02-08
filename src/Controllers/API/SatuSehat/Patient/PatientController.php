<?php

namespace Projects\WellmedGateway\Controllers\API\SatuSehat\Patient;

use Hanafalah\SatuSehat\Contracts\Schemas\Fhir\Patient\PatientSatuSehat;
use Illuminate\Http\Request;
use Projects\WellmedGateway\Controllers\API\ApiController;

class PatientController extends ApiController{
    public function __construct(
        protected PatientSatuSehat $__patient_schema
    )
    {
        parent::__construct();
    }

    public function store(Request $request){
        // request()->replace([
        //     'form' => request()->all()
        // ]);
        return $this->__patient_schema->storePatientSatuSehat();
    }

    public function update(Request $request){
        return $this->__patient_schema->updatePatientSatuSehat();
    }
}