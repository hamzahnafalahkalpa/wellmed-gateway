<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\LetterQueue;

use Projects\WellmedGateway\Requests\API\PatientEmr\LetterQueue\EnvironmentRequest;

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
