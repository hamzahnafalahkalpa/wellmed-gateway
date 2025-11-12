<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\Patient\VisitRegistration\VisitExamination\Examination\Practitioner;

use Projects\WellmedGateway\Requests\API\PatientEmr\VisitExamination\Examination\Practitioner\Environment;

class DeleteRequest extends Environment
{
  public function authorize(){
    return true;
  }

  public function rules(){
    return [
      'id' => ['nullable']
    ];
  }
}