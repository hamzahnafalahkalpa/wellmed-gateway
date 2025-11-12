<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\VisitPatient\VisitRegistration;

use Projects\WellmedGateway\Requests\API\PatientEmr\VisitRegistration\EnvironmentRequest;

class ViewRequest extends EnvironmentRequest
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
