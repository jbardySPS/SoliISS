<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $usuario = Auth::user();

        $conteoMisSolicitudes = null;
        $conteoPendientes     = null;
        $conteoTotal          = null;

        if ($usuario->esSolicitante()) {
            $conteoMisSolicitudes = Solicitud::delUsuario($usuario->usr_id)->count();
        } elseif ($usuario->esSupervisor()) {
            $conteoMisSolicitudes = Solicitud::enEstado([
                Solicitud::PENDIENTE, Solicitud::APROBADA, Solicitud::RECHAZADA,
            ])->count();
            $conteoPendientes = Solicitud::pendientesRevision()->count();
        } elseif ($usuario->esOperador()) {
            $conteoMisSolicitudes = Solicitud::paraOperador()->count();
            $conteoPendientes     = Solicitud::enEstado(Solicitud::APROBADA)->count();
        } elseif ($usuario->esAdmin()) {
            $conteoMisSolicitudes = Solicitud::count();
            $conteoPendientes     = Solicitud::pendientesRevision()->count();
            $conteoTotal          = Solicitud::count();
        }

        return view('dashboard', compact(
            'conteoMisSolicitudes',
            'conteoPendientes',
            'conteoTotal'
        ));
    }
}
