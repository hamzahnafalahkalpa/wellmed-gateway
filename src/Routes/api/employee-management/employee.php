<?php

use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\EmployeeManagement\Employee\EmployeeController;

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
Route::apiResource('employee', EmployeeController::class)
     ->parameters(['employee' => 'id']);