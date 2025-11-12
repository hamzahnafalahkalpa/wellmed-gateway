<?php

namespace Projects\WellmedGateway\Requests\API\ItemManagement\OpnameStock;

use Hanafalah\LaravelSupport\Requests\FormRequest;
class DeleteRequest extends FormRequest
{
  public function authorize()
  {
    return true;
  }

  public function rules()
  {
    return [];
  }
}