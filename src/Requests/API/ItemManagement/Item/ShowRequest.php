<?php

namespace Projects\WellmedGateway\Requests\API\ItemManagement\Item;

use Hanafalah\LaravelSupport\Requests\FormRequest;

class ShowRequest extends FormRequest
{
  protected $__entity = 'Item';

  public function authorize()
  {
    return true;
  }

  public function rules()
  {
    return [];
  }
}
