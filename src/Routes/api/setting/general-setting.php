<?php

use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\Setting\{
    EducationController,
    EncodingController,
    WorkspaceController
};

Route::group([
    'prefix' => '/general-setting',
    'as' => 'general-setting.'
],function(){
    Route::apiResource('/workspace',WorkspaceController::class)->parameters(['workspace' => 'uuid']);
    Route::group([
        'prefix' => '/workspace/{uuid}',
        'as' => 'workspace.'
    ],function(){
        Route::post('/logo',[WorkspaceController::class,'storeLogo'])->name('logo.store');
    });
    Route::apiResource('/encoding',EncodingController::class)->parameters(['encoding' => 'id']);
});
