<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->name('api.users.')->group(function () {
    Route::post('/', [UserController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('create');

    Route::get('/', [UserController::class, 'index'])
        ->middleware('throttle:60,1')
        ->name('index');
});
