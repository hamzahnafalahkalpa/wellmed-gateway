<?php

use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\Setting\{
    AgentController,
    BuildingController,
    ClassRoomItemCategoryController,
    ClassRoomController,
    CompanyController,
    ExternalFacilityController,
    PayerController,
    PosyanduController,
    PustuController,
    RoomController,
    RoomItemCategoryController
};

Route::group([
    'prefix' => '/infrastructure',
    'as' => 'infrastructure.'
],function(){
    Route::apiResource('/building',BuildingController::class)->parameters(['building' => 'id']);
    Route::apiResource('/room',RoomController::class)->parameters(['room' => 'id']);
    Route::apiResource('/room-item-category',RoomItemCategoryController::class)->parameters(['room-item-category' => 'id']);
    Route::apiResource('/class-room',ClassRoomController::class)->parameters(['class-room' => 'id']);
    Route::apiResource('/kiosk',BuildingController::class)->parameters(['kiosk' => 'id']);
    Route::apiResource('/agent',AgentController::class)->parameters(['agent' => 'id']);
    Route::apiResource('/payer',PayerController::class)->parameters(['payer' => 'id']);
    Route::apiResource('/company',CompanyController::class)->parameters(['company' => 'id']);
    Route::apiResource('/pustu',PustuController::class)->parameters(['pustu' => 'id']);
    Route::apiResource('/posyandu',PosyanduController::class)->parameters(['posyandu' => 'id']);
    Route::apiResource('/external-facility',ExternalFacilityController::class)->parameters(['external-facility' => 'id']);
});
