<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\Patient\VisitRegistration\VisitExamination\Assessment;

use Projects\WellmedGateway\Requests\API\PatientEmr\VisitExamination\EnvironmentRequest;

class StoreRequest extends Environment
{
  public function authorize(){
    return true;
  }
  
  public function rules(){    
    return [];
  }
}
