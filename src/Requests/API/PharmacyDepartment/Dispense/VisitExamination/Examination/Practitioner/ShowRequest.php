<?php

namespace Projects\WellmedGateway\Requests\API\PharmacyDepartment\Dispense\VisitExamination\Examination\Practitioner;

use Projects\WellmedGateway\Requests\API\VisitRegistration\VisitExamination\Examination\Practitioner\Environment;

class ShowRequest extends Environment
{
  public function authorize(){
    return true;
  }

  public function rules(){
    return [];
  }
}