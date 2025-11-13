<?php

use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\Setting\{
    PurchaseLabelController,
    ReceiveUnitController,
    SupplierController
};

Route::group([
    'prefix' => '/supply-chain',
    'as' => 'supply-chain.'
],function(){
    Route::apiResource('/receive-unit',ReceiveUnitController::class)->parameters(['receive-unit' => 'id']);
    Route::apiResource('/purchase-label',PurchaseLabelController::class)->parameters(['purchase-label' => 'id']);
    Route::apiResource('/supplier',SupplierController::class)->parameters(['supplier' => 'id']);
});

