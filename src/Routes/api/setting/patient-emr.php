<?php

use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\Setting\Examination\{
    AllergyStuffController,
    GcsStuffController,
    MonitoringCategoryController,
    PhysicalExaminationStuffController,
    TriageStuffController,
    VitalSignStuffController
};

Route::group([
    'prefix' => '/patient-emr',
    'as' => 'patient-emr.'
],function(){
    Route::group([
        'prefix' => '/examination',
        'as' => 'examination.'
    ],function(){
        Route::apiResource('/physical-examination-stuff',PhysicalExaminationStuffController::class)->parameters(['physical-examination-stuff' => 'id']);
        Route::apiResource('/allergy-stuff',AllergyStuffController::class)->parameters(['allergy-stuff' => 'id']);
        Route::apiResource('/vital-sign-stuff',VitalSignStuffController::class)->parameters(['vital-sign-stuff' => 'id']);
    });
});

