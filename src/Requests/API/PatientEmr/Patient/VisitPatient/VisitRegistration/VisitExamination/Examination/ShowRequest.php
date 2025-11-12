<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\Patient\VisitPatient\VisitRegistration\VisitExamination\Examination;

use Illuminate\Validation\Rule;
use Projects\WellmedGateway\Requests\API\PatientEmr\VisitExamination\Examination\Environment;

class ShowRequest extends Environment
{
  public function authorize(){
    return true;
  }

  public function rules(){
    $rule = Rule::in([
      'clinical-treatment',
      'lab-treatment',
      'radiology-treatment',
      'symptom',
      'pain-scale',
      'vital-sign',
      'anthropometry',
      'allergy',
      'g-c-s',
      'alloanamnese',
      'initial-diagnose',
      'primary-diagnose',
      'secondary-diagnose',
      'history-illness',
      'family-illness',
      'physical-examination',
      'triage',
      'eye-examination',
      'eye-refraction-examination',
      'eye-vision-color',
      'nose-examination',
      'ear-examination',
      'larynx-examination',
      'throat-examination',
      'medical-support',
      'lab-support',
      'radiology-support',
      'm-c-u-medical-history',
      'm-c-u-present-medical-history',
      'm-c-u-exam-summary',
      'm-c-u-package-summary',
      's-o-a-p',
      'medicine-prescription',
      'medic-tool-prescription',
      'mix-medicine-prescription',
      'eye-vision-color',
      'vaccine',
      'pharmacy-sale-examination',
      'food-handler-examination',
      'clasification-odonto',
    ]);
    return $this->setRules([
        'flag' => ['nullable', $rule],
    ]);
  }
}
