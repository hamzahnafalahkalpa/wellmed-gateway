<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\LetterQueue;

use Projects\WellmedGateway\Requests\API\PatientEmr\LetterQueue\EnvironmentRequest;

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