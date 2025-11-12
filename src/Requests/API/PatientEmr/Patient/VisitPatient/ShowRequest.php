<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\Patient\VisitPatient;

use Projects\WellmedGateway\Requests\API\PatientEmr\VisitPatient\EnvironmentRequest;

class ShowRequest extends EnvironmentRequest
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
