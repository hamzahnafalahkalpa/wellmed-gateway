<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\VisitPatient;

use Projects\WellmedGateway\Requests\API\PatientEmr\VisitPatient\EnvironmentRequest;

class DeleteRequest extends EnvironmentRequest
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