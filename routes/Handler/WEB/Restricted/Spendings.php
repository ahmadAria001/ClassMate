<?php

use App\Http\Controllers\SpendingController;
use Illuminate\Support\Facades\Route;

// profile
Route::get('/pengeluaran', SpendingController::class)->name('spending');
