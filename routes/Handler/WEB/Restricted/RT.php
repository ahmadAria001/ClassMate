<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\CivilianController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DocRequestController;
use App\Http\Controllers\DuesController;
use App\Http\Controllers\FinancialAssistanceController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\RtController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/warga-rt', [RtController::class, 'civilianBuilder'])->name('PendudukByRT');

Route::get('/arsip-penduduk', [CivilianController::class, 'viewArchived'])->name('ArsipPenduduk');

Route::get('/daftar-pengaduan', [ComplaintController::class, 'manageComplaintView'])->name('DaftarPengaduan');

Route::get('/daftar-pengajuan-surat', [DocRequestController::class, 'manageReqView'])->name('DaftarPermintaanSurat');

Route::get('/keuangan', [DuesController::class, 'manageDuesView'])->name('KeuanganRT');

Route::get('/daftar-bansos', [FinancialAssistanceController::class, 'manageFAView'])->name('DaftarBansos');

Route::get('/pengumuman', [NewsController::class, 'manageNewsView'])->name('Pengumuman');

Route::get('/kegiatan-warga', [ActivityController::class, 'manageActView'])->name('KegiatanWarga');

Route::get('/daftar-rt', [RtController::class, 'manageRTView'])->name('DaftarRT');
