<?php

use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\PatientEmr\Patient\PatientController;
use Projects\WellmedGateway\Controllers\API\PatientEmr\Patient\VisitPatient\{
    VisitRegistration\VisitExamination\Assessment\AssessmentController,
    VisitRegistration\Referral\ReferralController,
    VisitRegistration\VisitExamination\Examination\ExaminationController,
    VisitRegistration\VisitExamination\VisitExaminationController,
    VisitRegistration\VisitRegistrationController,
    VisitPatientController
};
use Projects\WellmedGateway\Controllers\API\PatientEmr\Patient\VisitRegistration\{
    VisitExamination\Assessment\AssessmentController as VRAssessmentController,
    VisitExamination\Examination\ExaminationController as VRExaminationController,
    VisitExamination\VisitExaminationController as VRVisitExaminationController,
    Referral\ReferralController as VRReferralController,
    VisitRegistrationController as VRVisitRegistrationController
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

Route::apiResource('/visit-examination',VRVisitExaminationController::class)->parameters(['visit-examination' => 'id']);
Route::group([
    "prefix" => "/visit-examination/{visit_examination_id}",
    "as"     => "visit-examination.show.",
],function() {
    Route::post('/examination',[VRExaminationController::class,'store'])->name('examination.store');
    Route::apiResource('/{morph}/assessment',VRAssessmentController::class)->parameters(['assessment' => 'id'])->only(['store','show','index']);
});
