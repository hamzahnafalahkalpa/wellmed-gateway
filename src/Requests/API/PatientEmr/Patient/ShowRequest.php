<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\Patient;

class ShowRequest extends PatientEnvironment
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
