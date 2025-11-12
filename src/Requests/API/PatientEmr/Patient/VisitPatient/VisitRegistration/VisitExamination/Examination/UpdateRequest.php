<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\Patient\VisitPatient\VisitRegistration\VisitExamination\Examination;


use Projects\WellmedGateway\Requests\API\PatientEmr\VisitExamination\Examination\Environment;

class UpdateRequest extends Environment
{
  public function authorize(){
    return true;
  }

  public function rules(){
    return $this->setRules([
        'visit_examination_id' => ['required'],
        'type'                 => ['required','in:commit, closed-emr-session'],
    ]);
  }
}