<?php

use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\Reporting\{
    ReportingController
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
Route::apiResource('/reporting/{reporting_type}',ReportingController::class)->only('index');