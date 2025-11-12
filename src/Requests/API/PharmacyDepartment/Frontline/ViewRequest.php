<?php

namespace Projects\WellmedGateway\Requests\API\PharmacyDepartment\Frontline;

use Projects\WellmedGateway\Requests\API\PatientEmr\VisitExamination\EnvironmentRequest;

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
