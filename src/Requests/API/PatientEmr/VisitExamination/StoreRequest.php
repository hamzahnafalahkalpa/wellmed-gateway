<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\VisitExamination;

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
