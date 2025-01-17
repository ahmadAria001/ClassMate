<?php

use App\Http\Controllers\DuesController;
use App\Http\Controllers\DuesMemberController;
use App\Http\Controllers\DuesPaymentLogController;
use App\Http\Middleware\Resources\CreateDues;
use App\Http\Middleware\Resources\DeleteDues;
use App\Http\Middleware\Resources\GetDues;
use App\Http\Middleware\Resources\UpdateDues;
use App\Http\Middleware\Resources\Dues\CreateLog;
use App\Http\Middleware\Resources\Dues\UpdateLog;
use App\Http\Middleware\Resources\Dues\GetLog;
use App\Http\Middleware\Resources\Dues\DeleteLog;
use App\Http\Middleware\Resources\Dues\Member\Create;
use App\Http\Middleware\Resources\Dues\Member\Update;
use App\Http\Middleware\Resources\Dues\Member\Delete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('/dues')->group(fn () => [
    Route::get('/types/{rt}', [DuesPaymentLogController::class, 'getDuesTypes'])->middleware(GetDues::class),
    Route::get('/member/{member}/{dues}/{page}', [DuesPaymentLogController::class, 'getMember'])->middleware(GetDues::class),

    Route::post('/member', [DuesMemberController::class, 'create'])->middleware(CreateDues::class),
    Route::put('/member', [DuesMemberController::class, 'edit'])->middleware(UpdateDues::class),
    Route::delete('/member', [DuesMemberController::class, 'destroy'])->middleware(DeleteDues::class),

    Route::get('/', [DuesController::class, 'get'])->middleware(GetDues::class),
    Route::get('/{filter}', [DuesController::class, 'get'])->middleware(GetDues::class),

    Route::post('/', [DuesController::class, 'create'])->middleware(CreateDues::class),
    Route::put('/', [DuesController::class, 'edit'])->middleware(UpdateDues::class),
    Route::delete('/', [DuesController::class, 'destroy'])->middleware(DeleteDues::class),
]);

Route::prefix('/dues-payment')->group(fn () => [
    Route::get('/', [DuesPaymentLogController::class, 'get'])->middleware(GetLog::class),
    Route::get('/{filter}', [DuesPaymentLogController::class, 'get'])->middleware(GetLog::class),
    Route::get('/rt/{rt_id}', [DuesPaymentLogController::class, 'getDuesRT'])->middleware(GetDues::class),

    Route::post('/', [DuesPaymentLogController::class, 'create'])->middleware(CreateLog::class),
    Route::put('/', [DuesPaymentLogController::class, 'edit'])->middleware(UpdateLog::class),
    Route::delete('/', [DuesPaymentLogController::class, 'destroy'])->middleware(DeleteLog::class),
]);
