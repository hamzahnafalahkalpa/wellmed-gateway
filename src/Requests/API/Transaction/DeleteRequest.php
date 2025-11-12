<?php

namespace Projects\WellmedGateway\Requests\API\Transaction;


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