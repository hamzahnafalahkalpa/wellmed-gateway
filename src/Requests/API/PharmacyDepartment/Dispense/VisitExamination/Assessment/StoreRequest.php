<?php

namespace Projects\WellmedGateway\Requests\API\PharmacyDepartment\Dispense\VisitExamination\Assessment;

use Projects\WellmedGateway\Requests\API\PharmacyDepartment\VisitExamination\EnvironmentRequest;

class StoreRequest extends Environment
{
  public function authorize(){
    return true;
  }
  
  public function rules(){    
    return [];
  }
}
