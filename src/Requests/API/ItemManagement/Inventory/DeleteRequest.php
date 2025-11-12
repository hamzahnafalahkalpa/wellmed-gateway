<?php

namespace Projects\WellmedGateway\Requests\API\ItemManagement\Inventory;

use Hanafalah\LaravelSupport\Requests\FormRequest;
class DeleteRequest extends FormRequest
{
  protected $__entity = 'Inventory';

  public function authorize()
  {
    return true;
  }

  public function rules()
  {
    return [];
  }
}