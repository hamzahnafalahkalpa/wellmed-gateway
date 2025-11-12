<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\Patient;

class ViewRequest extends PatientEnvironment
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