<?php

use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\Setting\{
    AgentController,
    BuildingController,
    ClassRoomController,
    CompanyController,
    PayerController,
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
});
