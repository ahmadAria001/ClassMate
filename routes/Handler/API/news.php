<?php

use App\Http\Controllers\NewsController;
use App\Http\Middleware\Resources\Environment\Create;
use App\Http\Middleware\Resources\Environment\Delete;
use App\Http\Middleware\Resources\Environment\Get;
use App\Http\Middleware\Resources\Environment\GetLatest;
use App\Http\Middleware\Resources\Environment\Update;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('/news')->group(fn () => [
    Route::get('/lts', [NewsController::class, 'getlts'])->middleware(GetLatest::class),
    Route::get('/', [NewsController::class, 'get'])->middleware(Get::class),
    Route::get('/{filter}', [NewsController::class, 'get'])->middleware(Get::class),

    Route::post('/', [NewsController::class, 'create'])->middleware(Create::class),
    Route::put('/', [NewsController::class, 'edit'])->middleware(Update::class),
    Route::delete('/', [NewsController::class, 'destroy'])->middleware(Delete::class),
]);
