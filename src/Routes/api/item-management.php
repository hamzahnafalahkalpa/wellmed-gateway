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
    "prefix" => "/item-management",
    "as"     => "item-management.",
],function() {
    include_once(__DIR__."/item-management/item.php");
    // include_once(__DIR__."/item-management/medical-item.php");
});