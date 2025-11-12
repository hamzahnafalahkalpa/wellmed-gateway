<?php

namespace Projects\WellmedGateway\Controllers\API;

use App\Http\Controllers\ApiController as ControllersApiController;
use Illuminate\Support\Facades\Artisan;
use Projects\WellmedGateway\Concerns\HasUser;

abstract class ApiController extends ControllersApiController
{
    use HasUser;

    public function __construct(){
        config(['micro-tenant.use-db-name' => true]);
        parent::__construct();
    }
}