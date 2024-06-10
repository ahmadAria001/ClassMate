<?php

use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\FinancialAssistanceController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// profile
Route::get('/profile', UserController::class)->name('Profile');
