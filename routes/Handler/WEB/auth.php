<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/login', function (Request $request) {
    if (str_contains($request->url(), 'api')) {
        $token = $request->bearerToken();

        if (!$token) {
            return Inertia::render('Login');
        }

        $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;
        if (!$token) {
            return Inertia::render('Login');
        }
    } else {
        $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;

        if (!$token) {
            return Inertia::render('Login');
        }

        return redirect('beranda');
    }
})->name('login');

Route::get('/register', function (Request $request) {
    if (str_contains($request->url(), 'api')) {
        $token = $request->bearerToken();

        if (!$token) {
            return Inertia::render('Register');
        }

        $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;
        if (!$token) {
            return Inertia::render('Register');
        }
    } else {
        $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;

        if (!$token) {
            return Inertia::render('Register');
        }

        return redirect('beranda');
    }
})->name('register');
