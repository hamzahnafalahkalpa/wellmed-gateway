<?php

use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\PatientEmr\VisitRegistration\{
    VisitExamination\VisitExaminationController,
    VisitExamination\Assessment\AssessmentController,
    VisitExamination\Examination\ExaminationController,
    Referral\ReferralController,
    VisitRegistrationController
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

Route::apiResource('/visit-registration',VisitRegistrationController::class)->parameters(['visit-registration' => 'id']);
Route::group([
    "prefix" => "/visit-registration/{visit_registration_id}",
    "as"     => "visit-registration.show.",
],function() {
    Route::apiResource('/referral',ReferralController::class)->parameters(['referral' => 'id']);
    Route::apiResource('/visit-examination',VisitExaminationController::class)->parameters(['visit-examination' => 'id']);
    Route::group([
        "prefix" => "/visit-examination/{visit_examination_id}",
        "as"     => "visit-examination.show.",
    ],function() {
        Route::post('/examination',[ExaminationController::class,'store'])->name('examination.store');
        Route::apiResource('/{morph}/assessment',AssessmentController::class)->parameters(['assessment' => 'id'])->only(['store','show','index']);
    });
});
