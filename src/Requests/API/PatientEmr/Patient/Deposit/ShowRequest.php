<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\Patient\Deposit;

use Projects\WellmedGateway\Requests\API\Transaction\Deposit\Environment;

class ShowRequest extends Environment
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
