<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/dues', function () {
    return Inertia::render('Auth/Dues');
})->name('dues');
