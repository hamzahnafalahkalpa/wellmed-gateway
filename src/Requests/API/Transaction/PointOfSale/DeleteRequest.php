<?php

namespace Projects\WellmedGateway\Requests\API\Transaction\PointOfSale;

use Projects\WellmedGateway\Requests\API\Transaction\Environment;

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