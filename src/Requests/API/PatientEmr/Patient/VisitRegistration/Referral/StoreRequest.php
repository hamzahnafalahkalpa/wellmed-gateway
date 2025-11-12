<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\Patient\VisitRegistration\Referral;

use Projects\WellmedGateway\Requests\API\PatientEmr\VisitRegistration\Referral\EnvironmentRequest;

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
