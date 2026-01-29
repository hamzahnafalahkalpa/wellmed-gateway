<?php

use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\Setting\{
    MedicServiceController,
    PatientTypeController,
    PatientTypeServiceController,
    MasterInformedConsentController,
};

Route::group([
    'prefix' => '/faskes-service',
    'as' => 'faskes-service.'
],function(){
    Route::apiResource('/patient-type',PatientTypeController::class)->parameters(['patient-type' => 'id']);
    Route::apiResource('/patient-type-service',PatientTypeServiceController::class)->parameters(['patient-type-service' => 'id']);
    Route::apiResource('/medic-service',MedicServiceController::class)->parameters(['medic-service' => 'id']);
});
