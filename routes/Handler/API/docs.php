<?php

use App\Http\Controllers\DocsController;
use App\Http\Middleware\Resources\Docs\Get;
use Illuminate\Support\Facades\Route;

Route::prefix('/docs')->group(function(){
    Route::get('/', [DocsController::class, 'get'])->middleware(Get::class);
    Route::get('/{filter}', [DocsController::class, 'get'])->middleware(Get::class);

});