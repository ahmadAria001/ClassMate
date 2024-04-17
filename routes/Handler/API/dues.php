<?php

use App\Http\Controllers\DuesController;
use App\Http\Middleware\Resources\CreateDues;
use App\Http\Middleware\Resources\DeleteDues;
use App\Http\Middleware\Resources\GetDues;
use App\Http\Middleware\Resources\UpdateDues;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('/dues')->group(fn () => [
    Route::get('/', [DuesController::class, 'get'])->middleware(GetDues::class),
    Route::get('/{filter}', [DuesController::class, 'get'])->middleware(GetDues::class),

    Route::post('/', [DuesController::class, 'create'])->middleware(CreateDues::class),
    Route::put('/', [DuesController::class, 'edit'])->middleware(UpdateDues::class),
    Route::delete('/', [DuesController::class, 'destroy'])->middleware(DeleteDues::class),
]);
