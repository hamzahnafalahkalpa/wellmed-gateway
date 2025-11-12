<?php

namespace Projects\WellmedGateway\Requests\API\ItemManagement\Inventory\HealthcareEquipment;

use Hanafalah\LaravelSupport\Requests\FormRequest;

class ShowRequest extends FormRequest
{
  protected $__entity = 'HealthcareEquipment';

  public function authorize()
  {
    return true;
  }

  public function rules()
  {
    return [];
  }
}
