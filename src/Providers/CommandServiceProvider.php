<?php

namespace Projects\WellmedGateway\Providers;

use Illuminate\Support\ServiceProvider;
use Projects\WellmedGateway\Commands;

class CommandServiceProvider extends ServiceProvider
{
    protected $__commands = [
    ];

    /**
     * Register the command.
     *
     * @return void
     */
    public function register()
    {
        $this->commands(config('wellmed-gateway.commands', $this->__commands));
    }

    public function provides()
    {
        return $this->__commands;
    }
}
