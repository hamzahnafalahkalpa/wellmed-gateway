<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\Patient\VisitPatient\VisitRegistration\VisitExamination;

use Projects\WellmedGateway\Requests\API\PatientEmr\VisitExamination\EnvironmentRequest;

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
