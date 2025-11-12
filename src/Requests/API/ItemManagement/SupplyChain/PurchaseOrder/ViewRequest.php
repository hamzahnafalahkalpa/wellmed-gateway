<?php

namespace Projects\WellmedGateway\Requests\API\ItemManagement\SupplyChain\PurchaseOrder;

use Hanafalah\LaravelSupport\Requests\FormRequest;

class ViewRequest extends FormRequest
{
  protected $__entity = 'PurchaseOrder';

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