<?php

use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\Unicode\Autolist\AutolistController;

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
Route::get('/autolist/{morph}/{type}',[AutolistController::class,'index'])->name('autolist.index');