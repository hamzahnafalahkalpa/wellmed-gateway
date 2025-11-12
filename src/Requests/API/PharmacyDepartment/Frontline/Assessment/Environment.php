<?php

namespace Projects\WellmedGateway\Requests\API\PharmacyDepartment\Frontline\Assessment;

use Hanafalah\LaravelSupport\Requests\FormRequest;

use Projects\WellmedGateway\Requests\API\PharmacyDepartment\VisitExamination\EnvironmentRequest;

class Environment extends FormRequest
{
  public function authorize(){
    return true;
  }
  
  public function rules(){    
    return [];
  }
}
