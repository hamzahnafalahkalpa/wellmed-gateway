<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\Patient\VisitPatient\VisitRegistration\VisitExamination\Assessment;

use Projects\WellmedGateway\Requests\API\VisitRegistration\VisitExamination\Assessment\Environment;

class StoreRequest extends Environment
{
  public function authorize(){
    return true;
  }
  
  public function rules(){    
    return [];
  }
}
