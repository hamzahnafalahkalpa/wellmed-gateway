<?php

namespace Projects\WellmedGateway\Requests\API\ItemManagement\SupplyChain\PurchaseRequest;

use Hanafalah\LaravelSupport\Requests\FormRequest;

class ViewRequest extends FormRequest
{
  protected $__entity = 'PurchaseRequest';

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