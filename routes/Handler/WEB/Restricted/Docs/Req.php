<?php

use App\Http\Controllers\DocRequestController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/status-pengajuan', DocRequestController::class)->name('statis-pengajuan');
