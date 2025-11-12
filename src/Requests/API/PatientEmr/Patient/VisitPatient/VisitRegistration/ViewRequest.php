<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\Patient\VisitPatient\VisitRegistration;

use Projects\WellmedGateway\Requests\API\PatientEmr\VisitPatient\EnvironmentRequest;

class ViewRequest extends EnvironmentRequest
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