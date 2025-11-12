<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\Patient\Deposit;

use Projects\WellmedGateway\Requests\API\Transaction\Deposit\Environment;

class StoreRequest extends Environment
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [];
    }
}
