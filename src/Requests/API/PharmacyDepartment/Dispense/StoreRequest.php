<?php

namespace Projects\WellmedGateway\Requests\API\PharmacyDepartment\Dispense;

use Projects\WellmedGateway\Requests\API\VisitRegistration\EnvironmentRequest;

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
