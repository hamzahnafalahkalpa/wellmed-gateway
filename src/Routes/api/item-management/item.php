<?php

use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\ItemManagement\Item\ItemController;

Route::group([
    'prefix' => '/item/import',
    'as'     => 'item.',
],function(){
    Route::post('/init',[ItemController::class,'init'])->name('init');
    Route::post('/chunk',[ItemController::class,'uploadChunk'])->name('chunk');
    Route::post('/process',[ItemController::class,'import'])->name('import');
    Route::post('/complete',[ItemController::class,'uploadComplete'])->name('import.complete');
    Route::get('/template',[ItemController::class,'downloadTemplate'])->name('import.template');
});
Route::apiResource('/item',ItemController::class)->parameters(['item' => 'id']);

