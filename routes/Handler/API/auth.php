<?php

use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\Auth\Logout;
use App\Http\Controllers\PasswordController;
use App\Http\Middleware\Resources\Password\Reset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('/auth')->group(fn () => [
    Route::post('/signin', Login::class),
    Route::get('/signout', Logout::class),
    Route::post('/pw/reset', [PasswordController::class,'reset'])->middleware(Reset::class),
]);
