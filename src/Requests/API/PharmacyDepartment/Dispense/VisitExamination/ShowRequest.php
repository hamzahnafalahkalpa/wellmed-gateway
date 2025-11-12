<?php

namespace Projects\WellmedGateway\Requests\API\PharmacyDepartment\Dispense\VisitExamination;

use Projects\WellmedGateway\Requests\API\PatientEmr\VisitExamination\EnvironmentRequest;

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
