<?php

namespace Projects\WellmedGateway\Requests\API\ItemManagement\MedicalItem;

use Hanafalah\LaravelSupport\Requests\FormRequest;

class ViewRequest extends FormRequest
{
  protected $__entity = 'MedicalItem';

  public function authorize()
  {
    return true;
  }

  public function rules()
  {
    return [
    ];
  }
}