<?php

use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\FinancialAssistanceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Sanctum\PersonalAccessToken;

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
Route::get('/lp-pengumuman', function () {
    return Inertia::render('LandingPagePengumuman');
});
Route::get('/lp-profile', function () {
    return Inertia::render('LandingPageProfile');
});

Route::get('/beranda', function () {
    $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;

    if (!$token) {
        return redirect('login');
    }

    if (!PersonalAccessToken::findToken($token)) {
        return redirect('login');
    }

    return Inertia::render('HomePage');
})->name('homepage');

Route::get('/404', function () {
    return Inertia::render('Page404');
})->name('404');

// RT dan RW

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
