<?php

namespace Projects\WellmedGateway\Requests\API\Transaction\Invoice\Refund;

use Projects\WellmedGateway\Requests\API\Transaction\Refund\Environment;

class DeleteRequest extends Environment
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