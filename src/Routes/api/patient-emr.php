<?php

use Illuminate\Support\Facades\Route;
use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\ModulePatient\Contracts\Data\PatientData;

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
    "prefix" => "/patient-emr",
    "as"     => "patient-emr.",
],function() {
    include_once(__DIR__."/patient-emr/patient.php");
    include_once(__DIR__."/patient-emr/visit-registration.php");
});