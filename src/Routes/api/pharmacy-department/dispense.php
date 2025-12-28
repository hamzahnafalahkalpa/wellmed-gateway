<?php

use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\PharmacyDepartment\Dispense\{
    VisitExamination\Assessment\AssessmentController,
    VisitExamination\Examination\ExaminationController,
    VisitExamination\VisitExaminationController,
    DispenseController
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

Route::apiResource('/dispense',DispenseController::class)->parameters(['dispense' => 'id']);
Route::group([
    "prefix" => "/dispense/{visit_registration_id}",
    "as"     => "dispense.show.",
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
