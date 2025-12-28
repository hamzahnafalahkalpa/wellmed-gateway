<?php

use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\PharmacyDepartment\Frontline\{
    Assessment\AssessmentController,
    Examination\ExaminationController,
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

Route::apiResource('/frontline',FrontlineController::class)->parameters(['frontline' => 'id']);
Route::group([
    "prefix" => "/frontline/{visit_examination_id}",
    "as"     => "frontline.show.",
],function() {
    Route::post('/examination',[ExaminationController::class,'store'])->name('examination.store');
    Route::put('/examination/{type}',[ExaminationController::class,'update'])->name('examination.update');
});
