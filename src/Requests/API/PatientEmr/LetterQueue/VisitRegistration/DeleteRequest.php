<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\LetterQueue\VisitRegistration;

use Projects\WellmedGateway\Requests\API\PatientEmr\VisitRegistration\EnvironmentRequest;

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
