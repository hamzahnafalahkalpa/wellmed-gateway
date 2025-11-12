<?php

use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\ItemManagement\MedicalItem\MedicalItemController;

Route::apiResource('/medical-item',MedicalItemController::class)->parameters(['medical-item' => 'id']);

