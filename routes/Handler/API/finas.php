<?php

use App\Http\Controllers\FinancialAssistanceController;
use App\Http\Middleware\Resources\FA\Create;
use App\Http\Middleware\Resources\FA\Delete;
use App\Http\Middleware\Resources\FA\Get;
use App\Http\Middleware\Resources\FA\Update;
use Illuminate\Support\Facades\Route;

Route::prefix('/bansos')->group(function () {
    Route::get('/', [FinancialAssistanceController::class, 'get'])->middleware(Get::class);
    Route::get('/{filter}', [FinancialAssistanceController::class, 'get'])->middleware(Get::class);

    Route::post('/', [FinancialAssistanceController::class, 'create'])->middleware(Create::class);
    Route::put('/', [FinancialAssistanceController::class, 'edit'])->middleware(Update::class);
    Route::delete('/', [FinancialAssistanceController::class, 'destroy'])->middleware(Delete::class);
});
