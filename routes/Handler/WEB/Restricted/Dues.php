<?php

use App\Http\Controllers\DuesController;
use Illuminate\Support\Facades\Route;

Route::get('/iuran', [DuesController::class, '__invoke'])->name('Iuran');
Route::get('/iuran/detail', [DuesController::class, 'show'])->name('Iuran');
Route::get('/log-pembayaran', [DuesController::class, 'log'])->name('Iuran');
