<?php

use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\Setting\SatuSehat\{
    EncounterIntegrationController,
    GeneralSettingController,
    PatientIntegrationController,
    SatuSehatLogController
};

Route::group([
    'prefix' => '/satu-sehat-integration',
    'as' => 'satu-sehat-integration.'
],function(){
    Route::apiResource('/general-setting',GeneralSettingController::class)->only('store')->parameters(['general-setting' => 'id']);
    Route::apiResource('/patient-integration',PatientIntegrationController::class)->parameters(['patient-integration' => 'id']);
    Route::apiResource('/encounter-integration',EncounterIntegrationController::class)->parameters(['encounter-integration' => 'id']);
    Route::apiResource('/satu-sehat-log',SatuSehatLogController::class)->only('destroy')->parameters(['satu-sehat-log' => 'id']);
});

