<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\Patient\VisitRegistration\VisitExamination\Assessment;

use Hanafalah\LaravelSupport\Requests\FormRequest;

use Projects\WellmedGateway\Requests\API\PatientEmr\VisitExamination\EnvironmentRequest;

class Environment extends FormRequest
{
  public function authorize(){
    return true;
  }
  
  public function rules(){    
    return [];
  }
}
