<?php

namespace Projects\WellmedGateway\Requests\API\ItemManagement\Inventory\OfficeSupply;

use Hanafalah\LaravelSupport\Requests\FormRequest;

class ViewRequest extends FormRequest
{
  protected $__entity = 'OfficeSupply';

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