<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SolicitudController;
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
        // Listado y detalle: todos los roles autenticados
        Route::get('/',          [SolicitudController::class, 'index'])->name('index');
        Route::get('/{solicitud}', [SolicitudController::class, 'show'])->name('show');

        // Crear / editar / eliminar: solicitante (rol 1) y admin (rol 4)
        Route::get('/create',              [SolicitudController::class, 'create'])->name('create');
        Route::post('/',                   [SolicitudController::class, 'store'])->name('store');
        Route::get('/{solicitud}/edit',    [SolicitudController::class, 'edit'])->name('edit');
        Route::put('/{solicitud}',         [SolicitudController::class, 'update'])->name('update');
        Route::delete('/{solicitud}',      [SolicitudController::class, 'destroy'])->name('destroy');

        // Transiciones de estado
        Route::post('/{solicitud}/enviar',   [SolicitudController::class, 'enviar'])->name('enviar');
        Route::post('/{solicitud}/aprobar',  [SolicitudController::class, 'aprobar'])->name('aprobar');
        Route::post('/{solicitud}/rechazar', [SolicitudController::class, 'rechazar'])->name('rechazar');
        Route::post('/{solicitud}/tomar',    [SolicitudController::class, 'tomar'])->name('tomar');
        Route::post('/{solicitud}/completar',[SolicitudController::class, 'completar'])->name('completar');
        Route::post('/{solicitud}/cancelar', [SolicitudController::class, 'cancelar'])->name('cancelar');
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
