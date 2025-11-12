<?php

namespace Projects\WellmedGateway\Requests\API\Transaction\Invoice;

use Projects\WellmedGateway\Requests\API\Transaction\Invoice\Environment;

class ShowRequest extends Environment
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
