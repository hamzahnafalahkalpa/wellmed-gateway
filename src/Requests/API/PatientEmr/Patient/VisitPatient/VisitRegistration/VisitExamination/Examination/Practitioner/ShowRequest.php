<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\Patient\VisitPatient\VisitRegistration\VisitExamination\Examination\Practitioner;

use Projects\WellmedGateway\Requests\API\PatientEmr\VisitExamination\Examination\Practitioner\Environment;

class ShowRequest extends Environment
{

  public function authorize(){
    return true;
  }

  public function rules(){
    return [];
  }
}