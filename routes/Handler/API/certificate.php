<?php

use App\Http\Controllers\CertificateController;
use App\Http\Middleware\Resources\Certificate\Create;
use App\Http\Middleware\Resources\Certificate\Delete;
use App\Http\Middleware\Resources\Certificate\Get;
use App\Http\Middleware\Resources\Certificate\Update;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('/certificate')->group(fn () => [
    Route::get('/', [CertificateController::class, 'get'])->middleware(Get::class),
    Route::get('/{filter}', [CertificateController::class, 'get'])->middleware(Get::class),

    Route::post('/', [CertificateController::class, 'create'])->middleware(Create::class),
    Route::put('/', [CertificateController::class, 'edit'])->middleware(Update::class),
    Route::delete('/', [CertificateController::class, 'destroy'])->middleware(Delete::class),
]);
