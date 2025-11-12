<?php

namespace Projects\WellmedGateway\Requests\API\Transaction;


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
