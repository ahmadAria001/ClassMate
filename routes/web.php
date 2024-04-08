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

Route::get('/civilian', function () {
    return Inertia::render('Auth/Civilian');
})->name('civilian');

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
