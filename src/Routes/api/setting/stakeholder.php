<?php

use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\Setting\{
    EducationController,
    FamilyRoleController,
    MaritalStatusController,
};

Route::group([
    'prefix' => '/stakeholder',
    'as' => 'stakeholder.'
],function(){
    Route::apiResource('/marital-status',MaritalStatusController::class)->parameters(['marital-status' => 'id']);
    Route::apiResource('/family-role',FamilyRoleController::class)->parameters(['family-role' => 'id']);
    Route::apiResource('/education',EducationController::class)->parameters(['education' => 'id']);
});

