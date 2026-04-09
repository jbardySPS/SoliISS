<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas públicas (sin autenticación)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

/*
|--------------------------------------------------------------------------
| Rutas protegidas (requieren login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |----------------------------------------------------------------------
    | Rutas de Solicitudes — Fase 1 (se irán completando)
    |----------------------------------------------------------------------
    */
    Route::prefix('solicitudes')->name('solicitudes.')->group(function () {
        // Se definen en la siguiente iteración de Fase 1
    });

    /*
    |----------------------------------------------------------------------
    | Rutas de Administración — solo Admin (rol 4)
    |----------------------------------------------------------------------
    */
    Route::middleware(['role:4'])->prefix('admin')->name('admin.')->group(function () {
        // Se definen cuando arranquemos el ABM de usuarios
    });

});
