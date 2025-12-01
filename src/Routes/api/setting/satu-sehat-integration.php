<?php

use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\Setting\SatuSehat\EncounterIntegrationController;
use Projects\WellmedGateway\Controllers\API\Setting\SatuSehat\GeneralSettingController;
use Projects\WellmedGateway\Controllers\API\Setting\SatuSehat\PatientIntegrationController;

Route::group([
    'prefix' => '/satu-sehat-integration',
    'as' => 'satu-sehat-integration.'
],function(){
    Route::apiResource('/general-setting',GeneralSettingController::class)->parameters(['general-setting' => 'id']);
    Route::apiResource('/patient-integration',PatientIntegrationController::class)->parameters(['patient-integration' => 'id']);
    Route::apiResource('/encounter-integration',EncounterIntegrationController::class)->parameters(['encounter-integration' => 'id']);
});

