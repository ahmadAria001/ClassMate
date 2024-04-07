<?php

use App\Http\Controllers\RtController;
use App\Http\Middleware\Resources\RT\Create;
use App\Http\Middleware\Resources\RT\Delete;
use App\Http\Middleware\Resources\RT\Get;
use App\Http\Middleware\Resources\RT\Update;
use Illuminate\Support\Facades\Route;

Route::prefix('/rt')->group(fn () => [
    Route::get('/', [RtController::class, 'get'])->middleware(Get::class),
    Route::get('/{filter}', [RtController::class, 'get'])->middleware(Get::class),

    Route::post('/', [RtController::class, 'create'])->middleware(Create::class),
    Route::put('/', [RtController::class, 'edit'])->middleware(Update::class),
    Route::delete('/', [RtController::class, 'destroy'])->middleware(Delete::class),
]);
