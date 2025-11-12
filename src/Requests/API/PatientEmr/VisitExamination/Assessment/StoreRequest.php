<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\VisitExamination\Assessment;

class StoreRequest extends Environment
{
  public function authorize(){
    return true;
  }
  
  public function rules(){    
    return [];
  }
}
