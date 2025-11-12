<?php

namespace Projects\WellmedGateway\Requests\API\ItemManagement\OpnameStock;

use Hanafalah\LaravelSupport\Requests\FormRequest;

class ShowRequest extends FormRequest
{
  protected $__entity = 'OpnameStock';

  public function authorize()
  {
    return true;
  }

  public function rules()
  {
    return [];
  }
}
