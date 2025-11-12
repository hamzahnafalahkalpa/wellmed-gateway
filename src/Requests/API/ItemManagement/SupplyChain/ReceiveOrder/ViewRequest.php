<?php

namespace Projects\WellmedGateway\Requests\API\ItemManagement\SupplyChain\ReceiveOrder;

use Hanafalah\LaravelSupport\Requests\FormRequest;

class ViewRequest extends FormRequest
{
  protected $__entity = 'ReceiveOrder';

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