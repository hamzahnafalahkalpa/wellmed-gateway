<?php

use Hanafalah\ApiHelper\Facades\ApiAccess;
use Hanafalah\LaravelSupport\Facades\LaravelSupport;
use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\Import\ImportController;
use Projects\WellmedGateway\Controllers\API\PatientEmr\Patient\PatientController;
use Projects\WellmedGateway\Controllers\API\Tenant\AddTenantController;
use Projects\WellmedGateway\Controllers\API\Xendit\XenditController;

ApiAccess::secure(function(){
    Route::group([
        'as' => 'api.',
        'prefix' => 'api/'
    ],function(){
        LaravelSupport::callRoutes(__DIR__.'/api');
    });
});
Route::post('api/patient/import/process',[PatientController::class,'import'])->name('import');
Route::post('api/add-tenant',[AddTenantController::class,'store'])->name('add-tenant.store');
Route::post('api/import/{type}',[ImportController::class,'store'])->name('import.store');
Route::post('api/xendit/paid',[XenditController::class,'store'])->name('api.xendit.paid');
