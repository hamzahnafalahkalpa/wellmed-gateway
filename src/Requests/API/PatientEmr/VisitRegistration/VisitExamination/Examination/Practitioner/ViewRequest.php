<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\VisitRegistration\VisitExamination\Examination\Practitioner;

use Projects\WellmedGateway\Requests\API\PatientEmr\VisitExamination\Examination\Practitioner\Environment;

class ViewRequest extends Environment
{
  public function authorize(){
    return true;
  }

  public function rules(){
    return [];
  }
}