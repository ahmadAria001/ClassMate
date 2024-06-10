<?php

use App\Http\Controllers\SpendingController;
use Illuminate\Support\Facades\Route;

// profile
Route::get('/spending', SpendingController::class)->name('spending');
