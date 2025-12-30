<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\Reservation;

class StoreRequest extends EnvironmentRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [];
    }
}
