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

Route::get('/beranda', function () {
    return Inertia::render('HomePage');
})->name('homepage');

Route::prefix('/auth')->group(fn() => [Route::post('/signin', Login::class)]);

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
