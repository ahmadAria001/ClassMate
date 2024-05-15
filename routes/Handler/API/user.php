<?php

use App\Http\Controllers\ProfileImageController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\Resources\User\Create;
use App\Http\Middleware\Resources\User\Delete;
use App\Http\Middleware\Resources\User\GET;
use App\Http\Middleware\Resources\User\Pict\Create as PictCreate;
use App\Http\Middleware\Resources\User\Pict\Get as PictGet;
use App\Http\Middleware\Resources\User\Update;
use Illuminate\Support\Facades\Route;

Route::prefix('/user')->group(
    fn() => [
        Route::prefix('/img')->group(fn()=>[
            Route::get('/{filter}',[ProfileImageController::class,'get'])->middleware(PictGet::class),
            Route::post('/',[ProfileImageController::class,'create'])->middleware(PictCreate::class),
        ]),

        Route::get('/{column}/{operator}/{value}', [UserController::class, 'getCustom'])->middleware(GET::class),

        Route::get('/', [UserController::class, 'get'])->middleware(GET::class),
        Route::get('/{filter}', [UserController::class, 'get'])->middleware(GET::class),

        Route::post('/', [UserController::class, 'create'])->middleware(Create::class),
        Route::put('/', [UserController::class, 'edit'])->middleware(Update::class),
        Route::delete('/', [UserController::class, 'destroy'])->middleware(Delete::class),
    ],
);
