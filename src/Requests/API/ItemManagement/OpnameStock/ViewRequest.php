<?php

namespace Projects\WellmedGateway\Requests\API\ItemManagement\OpnameStock;

use Hanafalah\LaravelSupport\Requests\FormRequest;

class ViewRequest extends FormRequest
{
  protected $__entity = 'OpnameStock';

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