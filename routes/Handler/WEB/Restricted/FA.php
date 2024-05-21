<?php

use App\Http\Controllers\FinancialAssistanceController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/status-bansos', FinancialAssistanceController::class)->name('status-bansos');
