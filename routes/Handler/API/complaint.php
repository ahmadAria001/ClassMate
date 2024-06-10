<?php

use App\Http\Controllers\ComplaintController;
use App\Http\Middleware\Resources\Docs\GetComplaint;
use Illuminate\Support\Facades\Route;

Route::prefix('/complaint')->group(function(){
    Route::get('/', [ComplaintController::class, 'get'])->middleware(GetComplaint::class);
    Route::get('/{filter}', [ComplaintController::class, 'get'])->middleware(GetComplaint::class);

});