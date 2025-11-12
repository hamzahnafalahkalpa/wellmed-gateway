<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\Patient\EMR;

use Illuminate\Validation\Rule;
use Projects\WellmedGateway\Requests\API\PatientEmr\Patient\EMR\PatientEnvironment;

class ViewRequest extends PatientEnvironment
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
