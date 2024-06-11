<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/civilian', function () {
    return Inertia::render('Auth/Civilian');
})->name('civilian');

Route::get('/status-pengajuan', function () {
    return Inertia::render('StatusPengajuan');
})->name('statis-pengajuan');

Route::get('/status-pengaduan', function () {
    return Inertia::render('StatusPengaduan');
})->name('status-pengaduan');
