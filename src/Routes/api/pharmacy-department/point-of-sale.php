<?php

use Illuminate\Support\Facades\Route;

use Projects\WellmedGateway\Controllers\API\PharmacyDepartment\PointOfSale\{
    Billing\Invoice\InvoiceController,
    Billing\BillingController,
    PointOfSaleController
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

Route::apiResource('/point-of-sale',PointOfSaleController::class)->parameters(['point-of-sale' => 'id']);
Route::group([
    "prefix" => "/point-of-sale/{transaction_id}",
    'as' => 'point-of-sale.show.'
],function(){
    Route::apiResource('/billing',BillingController::class)->parameters(['billing' => 'id']);
    Route::group([
        "prefix" => "/billing/{billing_id}",
        'as' => 'billing.show.'
    ],function(){
        Route::apiResource('/invoice',InvoiceController::class)->parameters(['invoice' => 'id']);
    });
});