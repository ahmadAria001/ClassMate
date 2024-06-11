<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/status-bansos', function () {
    return Inertia::render('StatusBansos');
})->name('status-bansos');
