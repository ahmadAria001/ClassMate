<?php

use App\Http\Controllers\ActivityController;
use App\Http\Middleware\Resources\Docs\GetActivity;
use Illuminate\Support\Facades\Route;

Route::prefix('/activity')->group(function(){
    Route::get('/', [ActivityController::class, 'get'])->middleware(GetActivity::class);
    Route::get('/{filter}', [ActivityController::class, 'get'])->middleware(GetActivity::class);

});