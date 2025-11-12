<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\VisitExamination\Examination;

class ShowRequest extends Environment
{
  public function authorize(){
    return true;
  }

  public function rules(){
    return [];
  }
}
