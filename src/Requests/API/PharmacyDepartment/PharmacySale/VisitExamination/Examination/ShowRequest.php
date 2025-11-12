<?php

namespace Projects\WellmedGateway\Requests\API\PharmacyDepartment\PharmacySale\VisitExamination\Examination;

use Projects\WellmedGateway\Requests\API\PatientEmr\VisitExamination\EnvironmentRequest as Environment;

class ShowRequest extends Environment
{
  public function authorize(){
    return true;
  }

  public function rules(){
    return [];
  }
}
