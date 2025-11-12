<?php

namespace Projects\WellmedGateway\Facades;

class WellmedGateway extends \Illuminate\Support\Facades\Facade
{
  /**
   * Get the registered name of the component.
   *
   * @return string
   */
  protected static function getFacadeAccessor()
  {
    return \Projects\WellmedGateway\Contracts\WellmedGateway::class;
  }
}
