<?php

use App\Http\Controllers\CivilianController;
use App\Http\Middleware\Resources\Civiliant;
use App\Http\Middleware\Resources\CiviliantCreate;
use App\Http\Middleware\Resources\CiviliantGet;
use App\Http\Middleware\Resources\DeleteCiviliant;
use App\Http\Middleware\Resources\UpdateCiviliant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('civiliant')->group(function () {
    Route::get('/', [CivilianController::class, 'get'])->middleware(CiviliantGet::class);
    Route::get('/{filter}', [CivilianController::class, 'get'])->middleware(CiviliantGet::class);

    Route::post('/', [CivilianController::class, 'create'])->middleware(CiviliantCreate::class);
    Route::put('/', [CivilianController::class, 'edit'])->middleware(UpdateCiviliant::class);
    Route::delete('/', [CivilianController::class, 'destroy'])->middleware(DeleteCiviliant::class);
});
