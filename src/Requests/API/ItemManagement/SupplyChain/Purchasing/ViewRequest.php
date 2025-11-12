<?php

namespace Projects\WellmedGateway\Requests\API\ItemManagement\SupplyChain\Purchasing;

use Hanafalah\LaravelSupport\Requests\FormRequest;

class ViewRequest extends FormRequest
{
  protected $__entity = 'Purchasing';

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