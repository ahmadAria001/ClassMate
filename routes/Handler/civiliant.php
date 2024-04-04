<?php

use App\Http\Controllers\CivilianController;
use App\Http\Middleware\Resources\Civiliant;
use App\Http\Middleware\Resources\CiviliantCreate;
use App\Http\Middleware\Resources\UpdateCiviliant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('civiliant')->group(function () {
    Route::get('/all', [CivilianController::class, 'getAll']);
    Route::post('/create', [CivilianController::class, 'create'])->middleware(CiviliantCreate::class);
    Route::put('/edit', [CivilianController::class, 'edit'])->middleware(UpdateCiviliant::class);
});
