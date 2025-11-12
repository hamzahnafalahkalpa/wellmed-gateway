<?php

use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\Setting\{
    SettingController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::apiResource('/setting',SettingController::class)->only('index');
Route::group([
    'prefix' => 'setting',
    'as' => 'setting.'
],function(){
    include __DIR__.'/setting/acl.php'; 
    include __DIR__.'/setting/faskes-service.php'; 
    include __DIR__.'/setting/finance.php';
    include __DIR__.'/setting/general-setting.php';
    include __DIR__.'/setting/patient-emr.php'; 
    include __DIR__.'/setting/treatment.php'; 
});