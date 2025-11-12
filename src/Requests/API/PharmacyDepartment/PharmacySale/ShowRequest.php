<?php

namespace Projects\WellmedGateway\Requests\API\PharmacyDepartment\PharmacySale;

use Projects\WellmedGateway\Requests\API\PatientEmr\VisitPatient\EnvironmentRequest;

class ShowRequest extends EnvironmentRequest
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
