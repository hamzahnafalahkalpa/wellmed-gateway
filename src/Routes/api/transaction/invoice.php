<?php

use Illuminate\Support\Facades\Route;

use Projects\WellmedGateway\Controllers\API\Transaction\Invoice\{
    Refund\RefundController,
    InvoiceController
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

Route::apiResource('/invoice',InvoiceController::class)->parameters(['invoice' => 'id']);
Route::group([
    'prefix' => '/invoice/{invoice_id}',
    'as' => 'invoice.show.'
],function(){
    Route::apiResource('/refund',RefundController::class)->parameters(['refund' => 'id']);
});