<?php

use App\Http\Controllers\FamilyController;
use App\Http\Middleware\Resources\Family\Create;
use App\Http\Middleware\Resources\Family\Delete;
use App\Http\Middleware\Resources\Family\Get;
use App\Http\Middleware\Resources\Family\Update;
use Illuminate\Support\Facades\Route;

Route::prefix('/family')->group(fn () => [
    Route::get('/', [FamilyController::class, 'get'])->middleware(Get::class),
    Route::get('/{filter}', [FamilyController::class, 'get'])->middleware(Get::class),

    Route::post('/', [FamilyController::class, 'create'])->middleware(Create::class),
    Route::put('/', [FamilyController::class, 'edit'])->middleware(Update::class),
    Route::delete('/', [FamilyController::class, 'destroy'])->middleware(Delete::class),
]);
