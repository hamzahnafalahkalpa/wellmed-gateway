<?php

namespace Projects\WellmedGateway\Controllers\API\Tenant;

use Projects\WellmedGateway\Controllers\API\ApiController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

class AddTenantController extends ApiController{
    public function store(Request $request){
        Artisan::call('db:seed',[
            '--class' => "Projects\WellmedBackbone\\Database\Seeders\\AddDatabaseSeeder"
        ]);   
    }
}