<?php

namespace Projects\WellmedGateway\Requests\API\ItemManagement\SupplyChain\ReceiveOrder;

use Hanafalah\LaravelSupport\Requests\FormRequest;

class ShowRequest extends FormRequest
{
  protected $__entity = 'ReceiveOrder';

  public function authorize()
  {
    return true;
  }

  public function rules()
  {
    return [];
  }
}
