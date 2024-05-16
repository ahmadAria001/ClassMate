<?php

use App\Http\Controllers\EnvironmentInfoController;
use App\Http\Middleware\Resources\Environment\Create;
use App\Http\Middleware\Resources\Environment\Delete;
use App\Http\Middleware\Resources\Environment\Get;
use App\Http\Middleware\Resources\Environment\GetLatest;
use App\Http\Middleware\Resources\Environment\Update;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('/environment')->group(fn () => [
    Route::get('/lts', [EnvironmentInfoController::class, 'getlts'])->middleware(GetLatest::class),
    Route::get('/', [EnvironmentInfoController::class, 'get'])->middleware(Get::class),
    Route::get('/{filter}', [EnvironmentInfoController::class, 'get'])->middleware(Get::class),
    Route::post('/', [EnvironmentInfoController::class, 'create'])->middleware(Create::class),
    Route::put('/', [EnvironmentInfoController::class, 'edit'])->middleware(Update::class),
    Route::delete('/', [EnvironmentInfoController::class, 'destroy'])->middleware(Delete::class),
]);
