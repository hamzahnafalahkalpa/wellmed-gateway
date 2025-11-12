<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\Patient\VisitPatient\VisitRegistration\VisitExamination\Examination;

use Projects\WellmedGateway\Requests\API\PatientEmr\VisitExamination\Examination\Environment;

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
