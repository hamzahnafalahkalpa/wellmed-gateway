<?php

use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\Setting\{
    RoleController, PermissionController
};

Route::group([
    'prefix' => '/acl',
    'as' => 'acl.'
],function(){
    Route::apiResource('/role',RoleController::class)->parameters(['role' => 'id']);
    Route::apiResource('/permission',PermissionController::class)->only('index')->parameters(['permission' => 'id']);
});
