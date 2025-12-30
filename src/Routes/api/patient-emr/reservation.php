<?php

use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\PatientEmr\DocumentType\Export\{
    DocumentTypeExportController,
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

Route::apiResource('/reservation',ReservationController::class)->parameters(['reservation' => 'id']);
