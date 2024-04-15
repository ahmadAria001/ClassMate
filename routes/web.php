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
    return Inertia::render('Home');
});
Route::get('/login', function () {
    return Inertia::render('Login');
})->name('login');

Route::get('/register', function () {
    return Inertia::render('Register');
})->name('register');

Route::get('/beranda', function () {
    return Inertia::render('HomePage');
})->name('homepage');

Route::get('/civilian', function () {
    return Inertia::render('Auth/Civilian');
})->name('civilian');

Route::get('/dues', function () {
    return Inertia::render('Auth/Dues');
})->name('dues');

Route::prefix('/auth')->group(fn () => [
    Route::post('/signin', Login::class)
]);


Route::get('/family', FamilyController::class);
Route::prefix('/family')->group(function () {
    Route::get('/', FamilyController::class);
    Route::get('/create', [FamilyController::class, 'createView']);
    Route::post('/store', [FamilyController::class, 'store']);
    Route::get('/detail/{family_id}', [FamilyController::class, 'detail']);
    Route::get('/edit/{family_id}', [FamilyController::class, 'editView']);
    Route::post('/update/{family_id}', [FamilyController::class, 'update']);
    Route::get('/delete/{family_id}', [FamilyController::class, 'delete']);
});

Route::get('/bansos', FinancialAssistanceController::class);
Route::prefix('/bansos')->group(function () {
    Route::get('/', FinancialAssistanceController::class);
    Route::get('/create', [FinancialAssistanceController::class, 'createView']);
    Route::post('/store', [FinancialAssistanceController::class, 'store']);
    Route::get('/detail/{bansos_id}', [FinancialAssistanceController::class, 'detail']);
    Route::get('/edit/{bansos_id}', [FinancialAssistanceController::class, 'editView']);
    Route::post('/update/{bansos_id}', [FinancialAssistanceController::class, 'update']);
    Route::get('/delete/{bansos_id}', [FinancialAssistanceController::class, 'delete']);
});

// 404
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
