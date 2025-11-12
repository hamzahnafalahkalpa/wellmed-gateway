<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => '/navigation',
    'as' => 'navigation.'
],function(){
    include __DIR__.'/navigation/menu.php';
    include __DIR__.'/navigation/profile.php';
    include __DIR__.'/navigation/digital-sign.php';
    include __DIR__.'/navigation/notification.php';
    include __DIR__.'/navigation/update-password.php';
});