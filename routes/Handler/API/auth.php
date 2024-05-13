<?php

use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\Auth\Logout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('/auth')->group(fn () => [
    Route::post('/signin', Login::class),
    Route::get('/signout',Logout::class)
]);
