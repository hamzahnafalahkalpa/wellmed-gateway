<?php

use Illuminate\Support\Facades\Route;

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
Route::group([
    "prefix" => "/pharmacy-department",
    "as"     => "pharmacy-department.",
],function() {
    include_once(__DIR__."/pharmacy-department/point-of-sale.php");
    include_once(__DIR__."/pharmacy-department/pharmacy-sale.php");
    include_once(__DIR__."/pharmacy-department/frontline.php");
    include_once(__DIR__."/pharmacy-department/dispense.php");
});