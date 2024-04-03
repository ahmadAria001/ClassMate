<?php

use App\Http\Controllers\Auth\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('/auth')->group(fn () => [
    Route::post('/signin', Login::class)
]);
