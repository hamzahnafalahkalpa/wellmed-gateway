<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\VisitRegistration\VisitExamination\Assessment;

use Projects\WellmedGateway\Requests\API\PatientEmr\VisitExamination\EnvironmentRequest;

class ShowRequest extends Environment
{
  public function authorize(){
    return true;
  }
  
  public function rules(){    
    return [];
  }
}
