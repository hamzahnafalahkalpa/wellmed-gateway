<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\Patient;

use Projects\WellmedGateway\Controllers\API\PatientEmr\EnvironmentController as GeneralEnvironmentController;

class EnvironmentController extends GeneralEnvironmentController{

    protected function recombineRequest(){
        if (isset(request()->search_value)){
            $this->__patient_schema->setParamLogic('or');
            request()->merge([
                'search_name'           => request()->search_value,
                'search_dob'            => request()->search_value,
                'search_nik'            => request()->search_value,
                'search_crew_id'        => request()->search_value,
                'search_medical_record' => request()->search_value,
                'search_value' => null
            ]);
        }
    }
}
