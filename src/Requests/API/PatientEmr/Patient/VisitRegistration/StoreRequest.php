<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\Patient\VisitRegistration;

use Projects\WellmedGateway\Requests\API\PatientEmr\VisitRegistration\EnvironmentRequest;

class StoreRequest extends EnvironmentRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
        ];
    }
}
