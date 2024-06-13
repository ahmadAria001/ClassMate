<?php

use App\Http\Controllers\SpendingController;
use App\Http\Middleware\Resources\Spendings\Create;
use App\Http\Middleware\Resources\Spendings\Delete;
use App\Http\Middleware\Resources\Spendings\Get;
use App\Http\Middleware\Resources\Spendings\Update;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('/spending')->group(fn () => [
    Route::get('/monthly-income', [SpendingController::class, 'getMonthlyIncomeLastSixMonths'])->middleware(Get::class),

    Route::get('/', [SpendingController::class, 'get'])->middleware(Get::class),
    Route::get('/{filter}', [SpendingController::class, 'get'])->middleware(Get::class),
    Route::get('/monthly-income', [SpendingController::class, 'getMonthlyIncomeLastSixMonths'])->middleware(Get::class),

    Route::get('/rt/{page}', [SpendingController::class, 'getByRT'])->middleware(Get::class),
    Route::get('/p/{page}', [SpendingController::class, 'getPaged'])->middleware(Get::class),

    Route::post('/', [SpendingController::class, 'create'])->middleware(Create::class),
    Route::put('/', [SpendingController::class, 'edit'])->middleware(Update::class),
    Route::delete('/', [SpendingController::class, 'destroy'])->middleware(Delete::class),
]);
