<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\VisitExamination\Assessment;

use Hanafalah\LaravelSupport\Requests\FormRequest;

class Environment extends FormRequest
{
  public function authorize(){
    return true;
  }
  
  public function rules(){    
    return [];
  }
}
