<?php

use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\Dashboard\DashboardController;

/*
|--------------------------------------------------------------------------
| Dashboard API Routes
|--------------------------------------------------------------------------
|
| These routes handle dashboard metrics retrieval from Elasticsearch.
| Supports filtering by daily, monthly, and yearly periods.
|
*/
Route::group([
    "prefix" => "/dashboard",
    "as"     => "dashboard.",
],function() {
    // GET /api/dashboard - Get dashboard metrics with Elasticsearch
    Route::get('/', [DashboardController::class, 'index'])
        ->name('index');
});