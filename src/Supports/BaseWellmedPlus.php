<?php

namespace Projects\WellmedGateway\Supports;

use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Hanafalah\LaravelSupport\Supports\PackageManagement;

class BaseWellmedGateway extends PackageManagement implements DataManagement
{
    protected $__config_name = 'wellmed-gateway';
    protected $__wellmed_lite = [];

    /**
     * A description of the entire PHP function.
     *
     * @param Container $app The Container instance
     * @throws Exception description of exception
     * @return void
     */
    public function __construct()
    {
        $this->setConfig($this->__config_name, $this->__wellmed_lite);
    }
}
