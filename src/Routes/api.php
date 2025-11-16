<?php

use Hanafalah\ApiHelper\Facades\ApiAccess;
use Hanafalah\LaravelSupport\Facades\LaravelSupport;
use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\Tenant\AddTenantController;

ApiAccess::secure(function(){
    Route::group([
        'as' => 'api.',
        'prefix' => 'api/'
    ],function(){
        LaravelSupport::callRoutes(__DIR__.'/api');
    });
});
Route::post('api/add-tenant',[AddTenantController::class,'store'])->name('add-tenant.store');
