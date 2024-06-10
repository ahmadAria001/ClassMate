<?php

use App\Http\Controllers\ComplaintController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/status-pengaduan', ComplaintController::class)->name('status-pengaduan');
