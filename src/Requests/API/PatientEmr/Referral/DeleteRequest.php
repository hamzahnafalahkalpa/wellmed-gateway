<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\Referral;

class DeleteRequest extends EnvironmentRequest
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
