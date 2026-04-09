<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * RoleMiddleware
 *
 * Uso en rutas:
 *   Route::middleware(['auth', 'role:3,4'])->group(...)  // Operador o Admin
 *   Route::middleware(['auth', 'role:4'])->group(...)    // Solo Admin
 *
 * Constantes de rol (ver App\Models\Rol):
 *   1 = Solicitante
 *   2 = Supervisor
 *   3 = Operador
 *   4 = Admin
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $usuario = Auth::user();

        // Convertir los parámetros a enteros
        $rolesPermitidos = array_map('intval', $roles);

        if (! in_array($usuario->USR_ROL_ID, $rolesPermitidos)) {
            abort(403, 'No tenés permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
