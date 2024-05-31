<?php

use App\Http\Controllers\RtController;
use App\Http\Middleware\Resources\RT\Create;
use App\Http\Middleware\Resources\RT\Delete;
use App\Http\Middleware\Resources\RT\Get;
use App\Http\Middleware\Resources\RT\GetWCV;
use App\Http\Middleware\Resources\RT\Update;
use Illuminate\Support\Facades\Route;

Route::prefix('/rt')->group(fn () => [
    Route::get('/cvl/{page}', [RtController::class, 'withCivils'])->middleware(GetWCV::class),
    Route::get('/cvl/{page}/{filter}', [RtController::class, 'withCivils'])->middleware(GetWCV::class),


    Route::get('/dd', [RtController::class, 'getDropDown'])->middleware(Get::class),
    Route::get('/', [RtController::class, 'get'])->middleware(Get::class),
    Route::get('/{page}', [RtController::class, 'get'])->middleware(Get::class),
    Route::get('/{page}/{filter}', [RtController::class, 'get'])->middleware(Get::class),

    Route::get('/', [RtController::class, 'get'])->middleware(Get::class),
    Route::post('/', [RtController::class, 'create'])->middleware(Create::class),
    Route::put('/', [RtController::class, 'edit'])->middleware(Update::class),
    Route::delete('/', [RtController::class, 'destroy'])->middleware(Delete::class),
]);
