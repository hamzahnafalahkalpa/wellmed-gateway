<?php

use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\PatientEmr\Patient\{
    OldVisit\OldVisitController,
    PatientController
};
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
use Projects\WellmedGateway\Controllers\API\PatientEmr\Patient\VisitExamination\{
    VisitExaminationController as PatientVisitExaminationController
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
    'prefix' => '/patient/import',
    'as'     => 'patient.',
],function(){
    Route::post('/init',[PatientController::class,'init'])->name('init');
    Route::post('/chunk',[PatientController::class,'uploadChunk'])->name('chunk');
    Route::post('/process',[PatientController::class,'import'])->name('import');
    Route::post('/complete',[PatientController::class,'uploadComplete'])->name('import.complete');
    Route::get('/template',[PatientController::class,'downloadTemplate'])->name('import.template');
});
Route::apiResource('/patient',PatientController::class)->parameters(['patient' => 'id']);
Route::group([
    "prefix" => "/patient/{patient_id}",
    "as"     => "patient.show.",
],function() {
    Route::apiResource('/old-visit',OldVisitController::class)->only('index','show')->parameters(['old-visit' => 'id']);
    Route::apiResource('/visit-patient',VisitPatientController::class)->parameters(['visit-patient' => 'id']);
    Route::group([
        "prefix" => "/visit-patient/{visit_patient_id}",
        "as"     => "visit-patient.show.",
    ],function() {
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
    });

    Route::apiResource('/visit-registration',VRVisitRegistrationController::class)->parameters(['visit-registration' => 'id']);
    Route::group([
        "prefix" => "/visit-registration/{visit_registration_id}",
        "as"     => "visit-registration.show.",
    ],function() {
        Route::apiResource('/referral',VRReferralController::class)->parameters(['referral' => 'id']);
        Route::apiResource('/visit-examination',VRVisitExaminationController::class)->parameters(['visit-examination' => 'id']);
        Route::group([
            "prefix" => "/visit-examination/{visit_examination_id}",
            "as"     => "visit-examination.show.",
        ],function() {
            Route::post('/examination',[VRExaminationController::class,'store'])->name('examination.store');
            Route::apiResource('/{morph}/assessment',VRAssessmentController::class)->parameters(['assessment' => 'id'])->only(['store','show','index']);
        });
    });

    Route::apiResource('/visit-examination',PatientVisitExaminationController::class)->parameters(['visit-examination' => 'id']);
});
