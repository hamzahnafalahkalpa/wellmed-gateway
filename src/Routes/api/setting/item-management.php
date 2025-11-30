<?php

use Hanafalah\ModuleItem\Contracts\Schemas\SupplyCategory;
use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\Setting\{
    BrandController,
    CompositionUnitController,
    DosageFormController,
    FreqUnitController,
    MedicalCompositionUnitController,
    MedicalNetUnitController,
    PackageFormController,
    TherapeuticClassController,
    UsageLocationController,
    UsageRouteController,
    NetUnitController,
    SellingFormController,
    SupplyCategoryController,
    UnitOfMeasureController,
    TrademarkController
};

Route::group([
    'prefix' => '/item-management',
    'as' => 'item-management.'
],function(){
    Route::apiResource('/brand',BrandController::class)->parameters(['brand' => 'id']);
    Route::apiResource('/net-unit',NetUnitController::class)->parameters(['net-unit' => 'id']);
    Route::apiResource('/selling-form',SellingFormController::class)->parameters(['selling-form' => 'id']);
    Route::apiResource('/unit-of-measure',UnitOfMeasureController::class)->parameters(['unit-of-measure' => 'id']);
    Route::apiResource('/composition-unit',CompositionUnitController::class)->parameters(['composition-unit' => 'id']);
    Route::group([
        'prefix' => '/inventory',
        'as' => 'inventory.'
    ],function(){
        Route::apiResource('/supply-category',SupplyCategoryController::class)->parameters(['supply-category' => 'id']);
    });
    Route::group([
        'prefix' => '/medicine',
        'as' => 'medicine.'
    ],function(){
        Route::apiResource('/freq-unit',FreqUnitController::class)->parameters(['freq-unit' => 'id']);
        Route::apiResource('/medical-composition-unit',MedicalCompositionUnitController::class)->parameters(['medical-composition-unit' => 'id']);
        Route::apiResource('/medical-net-unit',MedicalNetUnitController::class)->parameters(['medical-net-unit' => 'id']);
        // Route::apiResource('/prescription-mix-unit',PrescriptionMixUnitController::class)->parameters(['prescription-mix-unit' => 'id']);
        Route::apiResource('/dosage-form',DosageFormController::class)->parameters(['dosage-form' => 'id']);
        Route::apiResource('/package-form',PackageFormController::class)->parameters(['package-form' => 'id']);
        Route::apiResource('/therapeutic-class',TherapeuticClassController::class)->parameters(['therapeutic-class' => 'id']);
        Route::apiResource('/usage-location',UsageLocationController::class)->parameters(['usage-location' => 'id']);
        Route::apiResource('/usage-route',UsageRouteController::class)->parameters(['usage-route' => 'id']);
        Route::apiResource('/trademark',TrademarkController::class)->parameters(['trademark' => 'id']);
    });
    Route::group([
        'prefix' => '/supply-chain',
        'as' => 'supply-chain.'
    ],function(){
        Route::apiResource('/purchasing',TrademarkController::class)->parameters(['purchasing' => 'id']);
    });
});

