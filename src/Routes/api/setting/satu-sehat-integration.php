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
    Route::apiResource('/general-setting',GeneralSettingController::class)->only('index','store')->parameters(['general-setting' => 'id']);
    Route::apiResource('/patient-integration',PatientIntegrationController::class)->parameters(['patient-integration' => 'id']);
    Route::apiResource('/encounter-integration',EncounterIntegrationController::class)->parameters(['encounter-integration' => 'id']);
    Route::apiResource('/satu-sehat-log',SatuSehatLogController::class)->only('destroy')->parameters(['satu-sehat-log' => 'id']);

    // Dashboard routes - Current/Live data
    Route::get('/dashboard', [SatuSehatLogController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/current', [SatuSehatLogController::class, 'currentDashboard'])->name('dashboard.current');
    Route::get('/dashboard/example', [SatuSehatLogController::class, 'exampleDashboard'])->name('dashboard.example');
    Route::post('/dashboard/update-current', [SatuSehatLogController::class, 'updateCurrentCount'])->name('dashboard.update-current');
    Route::post('/dashboard/update-synced', [SatuSehatLogController::class, 'updateSyncedCount'])->name('dashboard.update-synced');
    Route::post('/dashboard/increment-synced', [SatuSehatLogController::class, 'incrementSyncedCount'])->name('dashboard.increment-synced');
    Route::post('/dashboard/bulk-update', [SatuSehatLogController::class, 'bulkUpdateDashboard'])->name('dashboard.bulk-update');

    // Dashboard routes - Monthly snapshots (for future historical data)
    Route::get('/dashboard/snapshots', [SatuSehatLogController::class, 'availableSnapshots'])->name('dashboard.snapshots');
    Route::get('/dashboard/snapshots/{month}', [SatuSehatLogController::class, 'monthlySnapshot'])->name('dashboard.snapshots.show');
    Route::post('/dashboard/snapshots/{month}', [SatuSehatLogController::class, 'storeSnapshot'])->name('dashboard.snapshots.store');
});
Route::apiResource('/satu-sehat-integration',SatuSehatLogController::class)->only('index','store')->parameters(['satu-sehat-integration' => 'id']);

