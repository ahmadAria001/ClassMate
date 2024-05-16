<?php

use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\FinancialAssistanceController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return Inertia::render('LandingPage');
});

Route::get('/beranda', function () {
    return Inertia::render('HomePage');
})->name('homepage');

Route::get('/404', function () {
    return Inertia::render('Page404');
})->name('404');

// profile
Route::get('/profile', function () {
    return Inertia::render('Profile');
})->name('Profile');

// warga
Route::get('/status-pengaduan', function () {
    return Inertia::render('StatusPengaduan');
})->name('StatusPengaduan');
Route::get('/status-pengajuan', function () {
    return Inertia::render('StatusPengajuan');
})->name('Statuspengajuan');
Route::get('/status-bansos', function () {
    return Inertia::render('StatusBansos');
})->name('StatusBansos');

// RT dan RW
Route::get('/warga-rt', function () {
    return Inertia::render('PendudukByRT');
})->name('PendudukByRT');
Route::get('/arsip-penduduk', function () {
    return Inertia::render('ArsipPenduduk');
})->name('ArsipPenduduk');
Route::get('/daftar-pengaduan', function () {
    return Inertia::render('DaftarPengaduan');
})->name('DaftarPengaduan');
Route::get('/daftar-pengajuan-surat', function () {
    return Inertia::render('DaftarPermintaanSurat');
})->name('DaftarPermintaanSurat');
Route::get('/keuangan', function () {
    return Inertia::render('KeuanganRT');
})->name('KeuanganRT');
Route::get('/daftar-bansos', function () {
    return Inertia::render('DaftarBansos');
})->name('DaftarBansos');
Route::get('/pengumuman', function () {
    return Inertia::render('Pengumuman');
})->name('Pengumuman');
Route::get('/kegiatan-warga', function () {
    return Inertia::render('KegiatanWarga');
})->name('KegiatanWarga');

Route::get('/daftar-rt', function () {
    return Inertia::render('DaftarRT');
})->name('DaftarRT');

Route::prefix('/auth')->group(fn () => [Route::post('/signin', Login::class)]);

function loadRoutesWEB($dir)
{
    if (!is_dir($dir)) {
        return;
    }

    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        }

        $filePath = $dir . '/' . $file;
        if (is_file($filePath) && pathinfo($filePath)['extension'] === 'php') {
            require_once $filePath;
        } elseif (is_dir($filePath)) {
            loadRoutes($filePath);
        }
    }
}

loadRoutesWEB(__DIR__ . '/Handler/WEB');
