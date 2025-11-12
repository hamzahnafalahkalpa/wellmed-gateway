<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\VisitRegistration\Referral;

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
