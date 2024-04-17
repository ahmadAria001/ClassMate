<?php

use App\Http\Controllers\DocumentationController;
use App\Http\Middleware\Resources\Docs\GetDocumentation;
use Illuminate\Support\Facades\Route;

Route::prefix('/documentation')->group(function(){
    Route::get('/', [DocumentationController::class, 'get'])->middleware(GetDocumentation::class);
    Route::get('/{filter}', [DocumentationController::class, 'get'])->middleware(GetDocumentation::class);

});