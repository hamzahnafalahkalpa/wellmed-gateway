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
    "prefix" => "/transaction",
    "as"     => "transaction.",
],function() {
    include_once(__DIR__."/transaction/point-of-sale.php");
    include_once(__DIR__."/transaction/billing.php");
    // include_once(__DIR__."/transaction/invoice.php");
});