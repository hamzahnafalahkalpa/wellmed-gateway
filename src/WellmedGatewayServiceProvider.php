<?php

namespace Projects\WellmedGateway;

use Hanafalah\LaravelSupport\{
    Concerns\NowYouSeeMe,
    Supports\PathRegistry
};
use Projects\WellmedGateway\{
    WellmedGateway,
    Providers,
};

class WellmedGatewayServiceProvider extends WellmedGatewayEnvironment
{
    use NowYouSeeMe;

    public function register()
    {
        $this->registerMainClass(WellmedGateway::class,false)
            ->registerCommandService(Providers\CommandServiceProvider::class)
            ->registers('*');

        // Register Dashboard Schema binding
        $this->app->bind(
            \Projects\WellmedGateway\Contracts\Schemas\Dashboard::class,
            \Projects\WellmedGateway\Schemas\Dashboard::class
        );
    }

    public function boot(){      
        $this->app->booted(function(){
            $this->app->singleton(PathRegistry::class, function(){
                $registry = new PathRegistry();

                $config = config("wellmed-gateway");
                foreach ($config['libs'] as $key => $lib) $registry->set($key, 'projects'.$lib);
                
                return $registry;
            });

        });
    }    
}
