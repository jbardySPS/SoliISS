<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\UserController;
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

        // Crear / editar / eliminar: solicitante (rol 1) y admin (rol 4)
        Route::get('/create',              [SolicitudController::class, 'create'])->name('create');
        Route::post('/',                   [SolicitudController::class, 'store'])->name('store');
        Route::get('/{solicitud}',         [SolicitudController::class, 'show'])->name('show');
        Route::get('/{solicitud}/edit',    [SolicitudController::class, 'edit'])->name('edit');
        Route::put('/{solicitud}',         [SolicitudController::class, 'update'])->name('update');
        Route::delete('/{solicitud}',      [SolicitudController::class, 'destroy'])->name('destroy');

        // Transiciones de estado (flujo ITSM 9 estados)
        Route::post('/{solicitud}/enviar',        [SolicitudController::class, 'enviar'])->name('enviar');
        Route::post('/{solicitud}/tomar-revision',[SolicitudController::class, 'tomarRevision'])->name('tomarRevision');
        Route::post('/{solicitud}/aprobar',       [SolicitudController::class, 'aprobar'])->name('aprobar');
        Route::post('/{solicitud}/rechazar',      [SolicitudController::class, 'rechazar'])->name('rechazar');
        Route::post('/{solicitud}/asignar',       [SolicitudController::class, 'asignar'])->name('asignar');
        Route::post('/{solicitud}/iniciar',       [SolicitudController::class, 'iniciar'])->name('iniciar');
        Route::post('/{solicitud}/resolver',      [SolicitudController::class, 'resolver'])->name('resolver');
        Route::post('/{solicitud}/cerrar',        [SolicitudController::class, 'cerrar'])->name('cerrar');
        Route::post('/{solicitud}/comentar',      [SolicitudController::class, 'comentar'])->name('comentar');
    });

    /*
    |----------------------------------------------------------------------
    | Rutas de Administración — solo Admin (rol 4)
    |----------------------------------------------------------------------
    */
    Route::middleware(['role:4'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/usuarios',                    [UserController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/create',             [UserController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios',                   [UserController::class, 'store'])->name('usuarios.store');
        Route::get('/usuarios/{usuario}/edit',     [UserController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/{usuario}',          [UserController::class, 'update'])->name('usuarios.update');
        Route::post('/usuarios/{usuario}/toggle',  [UserController::class, 'toggleActivo'])->name('usuarios.toggle');
    });

});
