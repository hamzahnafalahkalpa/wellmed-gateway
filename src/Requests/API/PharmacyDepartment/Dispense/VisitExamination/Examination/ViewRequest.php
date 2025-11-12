<?php

namespace Projects\WellmedGateway\Requests\API\PharmacyDepartment\Dispense\VisitExamination\Examination;

use Projects\WellmedGateway\Requests\API\VisitRegistration\VisitExamination\Examination\Practitioner\Environment;

class ViewRequest extends Environment
{
  public function authorize(){
    return true;
  }
  
  public function rules(){    
    return [
      'visit_examination_id' => ['required',$this->idValidation('VisitExamination')]
    ];
  }
}
