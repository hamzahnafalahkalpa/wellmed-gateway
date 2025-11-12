<?php

namespace Projects\WellmedGateway\Requests\API\Transaction\PointOfSale\Billing\Invoice;

use Projects\WellmedGateway\Requests\API\Transaction\Invoice\Environment;

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