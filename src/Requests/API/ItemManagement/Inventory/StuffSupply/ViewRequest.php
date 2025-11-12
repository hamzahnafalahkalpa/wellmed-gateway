<?php

namespace Projects\WellmedGateway\Requests\API\ItemManagement\Inventory\StuffSupply;

use Hanafalah\LaravelSupport\Requests\FormRequest;

class ViewRequest extends FormRequest
{
  protected $__entity = 'StuffSupply';

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