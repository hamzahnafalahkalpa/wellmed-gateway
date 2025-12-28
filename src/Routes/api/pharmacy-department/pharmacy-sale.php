<?php

use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\PharmacyDepartment\PharmacySale\{
    VisitExamination\Assessment\AssessmentController,
    VisitExamination\Examination\ExaminationController,
    VisitExamination\VisitExaminationController,
    PharmacySaleController
};

use Projects\WellmedGateway\Controllers\API\PharmacyDepartment\Frontline\{
    Examination\ExaminationController as FrontlineExaminationController,
    FrontlineController
};
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    "prefix" => "/pharmacy-sale",
    "as"     => "pharmacy-sale.",
],function() {
    Route::apiResource('/frontline',FrontlineController::class)->parameters(['frontline' => 'id']);
    Route::group([
        "prefix" => "/frontline/{visit_examination_id}",
        "as"     => "frontline.show.",
    ],function() {
        Route::post('/examination',[FrontlineExaminationController::class,'store'])->name('examination.store');
        Route::put('/examination/{type}',[FrontlineExaminationController::class,'update'])->name('examination.update');
    });
});
Route::apiResource('/pharmacy-sale',PharmacySaleController::class)->parameters(['pharmacy-sale' => 'id']);
Route::group([
    "prefix" => "/pharmacy-sale/{visit_registration_id}",
    "as"     => "pharmacy-sale.show.",
],function() {
    Route::apiResource('/visit-examination',VisitExaminationController::class)->parameters(['visit-examination' => 'id']);
    Route::group([
        "prefix" => "/visit-examination/{visit_examination_id}",
        "as"     => "visit-examination.show.",
    ],function() {
        Route::post('/examination',[ExaminationController::class,'store'])->name('examination.store');
        Route::apiResource('/{morph}/assessment',AssessmentController::class)->parameters(['assessment' => 'id'])->only(['store','show','index']);
    });
});
