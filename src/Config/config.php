<?php 

return [
    "namespace"     => "Projects\WellmedGateway",
    "service_name"  => "WellmedGateway",
    "paths"         => [
        "local_path"   => 'projects',
        "base_path"    => __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR
    ],
    "libs"           => [
        'migration' => 'Database/Migrations',
        'database' => 'Database',
        'model' => 'Models',
        'controller' => 'Controllers',
        'provider' => 'Providers',
        'contract' => 'Contracts',
        'concern' => 'Concerns',
        'command' => 'Commands',
        'route' => 'Routes',
        'observer' => 'Observers',
        'policy' => 'Policies',
        'transformer' => 'Transformers',
        'seeder' => 'Database/Seeders',
        'middleware' => 'Middleware',
        'request' => 'Requests',
        'support' => 'Supports',
        'view' => 'Views',
        'schema' => 'Schemas',
        'facade' => 'Facades',
        'config' => 'Config',
        'import' => 'Imports',
        'data' => 'Data',
        'resource' => 'Resources',
    ],
];