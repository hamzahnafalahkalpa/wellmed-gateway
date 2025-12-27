<?php

use Illuminate\Support\Facades\Route;

use Projects\WellmedGateway\Controllers\API\Transaction\Billing\{
    BillingController
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

Route::apiResource('/billing',BillingController::class)->parameters(['billing' => 'id']);
Route::group([
    "prefix" => "/billing/{billing_id}",
    'as' => 'billing.show.'
],function(){
    Route::get('/kwitansi',[BillingController::class,'kwitansi'])->name('kwitansi');
});