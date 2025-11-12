<?php

use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\ItemManagement\Item\ItemController;

Route::apiResource('/item',ItemController::class)->parameters(['item' => 'id']);

