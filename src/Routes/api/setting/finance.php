<?php

use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\Setting\{
    PaymentMethodController,
    TariffComponentController
};

Route::group([
    'prefix' => '/finance',
    'as' => 'finance.'
],function(){
    Route::apiResource('/payment-method',PaymentMethodController::class)->parameters(['payment-method' => 'id']);
    Route::apiResource('/tariff-component',TariffComponentController::class)->parameters(['tariff-component' => 'id']);
});
