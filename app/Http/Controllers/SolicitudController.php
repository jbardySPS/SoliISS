<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SolicitudController extends Controller
{
    // ── INDEX ───────────────────────────────────────────────────────────────
    // Cada rol ve una vista filtrada de las solicitudes.
    public function index(Request $request): View
    {
        $usuario = Auth::user();
        $query   = Solicitud::with(['solicitante', 'supervisor', 'operador'])
                            ->orderBy('sol_creado', 'desc');

        // Filtro por estado opcional (desde URL ?estado=pendiente)
        $estadoFiltro = $request->query('estado');

        if ($usuario->esSolicitante()) {
            // Solo ve las propias
            $query->delUsuario($usuario->usr_id);
        } elseif ($usuario->esSupervisor()) {
            // Ve las pendientes y las que ya revisó
            $query->enEstado([Solicitud::PENDIENTE, Solicitud::APROBADA, Solicitud::RECHAZADA]);
        } elseif ($usuario->esOperador()) {
            // Ve aprobadas y en_proceso
            $query->paraOperador();
        }
        // Admin ve todo

        if ($estadoFiltro) {
            $query->enEstado($estadoFiltro);
        }

        $solicitudes = $query->paginate(20)->withQueryString();

        return view('solicitudes.index', compact('solicitudes', 'estadoFiltro'));
    }

    // ── CREATE ──────────────────────────────────────────────────────────────
    public function create(): View|RedirectResponse
    {
        if (! Auth::user()->tieneRol([1, 4])) {
            abort(403, 'No tenés permiso para crear solicitudes.');
        }

        return view('solicitudes.create');
    }

    // ── STORE ───────────────────────────────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        if (! Auth::user()->tieneRol([1, 4])) {
            abort(403);
        }

        $datos = $request->validate([
            'sol_tipo'        => ['required', 'string', 'in:' . implode(',', array_keys(Solicitud::TIPOS))],
            'sol_descripcion' => ['required', 'string', 'min:10', 'max:2000'],
            'sol_prioridad'   => ['required', 'in:baja,media,alta'],
            'enviar'          => ['nullable', 'boolean'],
        ], [
            'sol_tipo.required'        => 'Seleccioná un tipo de solicitud.',
            'sol_tipo.in'              => 'Tipo de solicitud inválido.',
            'sol_descripcion.required' => 'La descripción es obligatoria.',
            'sol_descripcion.min'      => 'La descripción debe tener al menos 10 caracteres.',
            'sol_descripcion.max'      => 'La descripción no puede superar 2000 caracteres.',
            'sol_prioridad.required'   => 'Seleccioná la prioridad.',
            'sol_prioridad.in'         => 'Prioridad inválida.',
        ]);

        $estado = $request->boolean('enviar') ? Solicitud::PENDIENTE : Solicitud::BORRADOR;

        $solicitud = Solicitud::create([
            'sol_usr_id'      => Auth::id(),
            'sol_tipo'        => $datos['sol_tipo'],
            'sol_descripcion' => $datos['sol_descripcion'],
            'sol_prioridad'   => $datos['sol_prioridad'],
            'sol_estado'      => $estado,
        ]);

        $msg = $estado === Solicitud::PENDIENTE
            ? 'Solicitud enviada correctamente.'
            : 'Solicitud guardada como borrador.';

        return redirect()->route('solicitudes.show', $solicitud)
                         ->with('status', $msg);
    }

    // ── SHOW ────────────────────────────────────────────────────────────────
    public function show(Solicitud $solicitud): View
    {
        $usuario = Auth::user();

        // Solicitante solo puede ver las propias
        if ($usuario->esSolicitante() && $solicitud->sol_usr_id !== $usuario->usr_id) {
            abort(403);
        }

        $solicitud->load(['solicitante', 'supervisor', 'operador']);

        return view('solicitudes.show', compact('solicitud'));
    }

    // ── EDIT ────────────────────────────────────────────────────────────────
    public function edit(Solicitud $solicitud): View|RedirectResponse
    {
        $usuario = Auth::user();

        if (! $this->puedeEditar($usuario, $solicitud)) {
            abort(403, 'No podés editar esta solicitud.');
        }

        return view('solicitudes.edit', compact('solicitud'));
    }

    // ── UPDATE ──────────────────────────────────────────────────────────────
    public function update(Request $request, Solicitud $solicitud): RedirectResponse
    {
        $usuario = Auth::user();

        if (! $this->puedeEditar($usuario, $solicitud)) {
            abort(403);
        }

        $datos = $request->validate([
            'sol_tipo'        => ['required', 'string', 'in:' . implode(',', array_keys(Solicitud::TIPOS))],
            'sol_descripcion' => ['required', 'string', 'min:10', 'max:2000'],
            'sol_prioridad'   => ['required', 'in:baja,media,alta'],
        ], [
            'sol_tipo.required'        => 'Seleccioná un tipo de solicitud.',
            'sol_tipo.in'              => 'Tipo de solicitud inválido.',
            'sol_descripcion.required' => 'La descripción es obligatoria.',
            'sol_descripcion.min'      => 'La descripción debe tener al menos 10 caracteres.',
            'sol_descripcion.max'      => 'La descripción no puede superar 2000 caracteres.',
            'sol_prioridad.required'   => 'Seleccioná la prioridad.',
            'sol_prioridad.in'         => 'Prioridad inválida.',
        ]);

        $solicitud->update($datos);

        return redirect()->route('solicitudes.show', $solicitud)
                         ->with('status', 'Solicitud actualizada.');
    }

    // ── DESTROY ─────────────────────────────────────────────────────────────
    public function destroy(Solicitud $solicitud): RedirectResponse
    {
        $usuario = Auth::user();

        if (! $this->puedeEditar($usuario, $solicitud)) {
            abort(403);
        }

        $solicitud->delete();

        return redirect()->route('solicitudes.index')
                         ->with('status', 'Solicitud eliminada.');
    }

    // ── ENVIAR (borrador → pendiente) ────────────────────────────────────────
    public function enviar(Solicitud $solicitud): RedirectResponse
    {
        $usuario = Auth::user();

        if (! $usuario->tieneRol([1, 4])) abort(403);
        if ($solicitud->sol_usr_id !== $usuario->usr_id && ! $usuario->esAdmin()) abort(403);
        if (! $solicitud->esBorrador()) {
            return back()->with('error', 'Solo se pueden enviar solicitudes en estado borrador.');
        }

        $solicitud->update(['sol_estado' => Solicitud::PENDIENTE]);

        return redirect()->route('solicitudes.show', $solicitud)
                         ->with('status', 'Solicitud enviada. Queda pendiente de revisión.');
    }

    // ── APROBAR (pendiente → aprobada) ───────────────────────────────────────
    public function aprobar(Request $request, Solicitud $solicitud): RedirectResponse
    {
        if (! Auth::user()->tieneRol([2, 4])) abort(403);
        if (! $solicitud->esPendiente()) {
            return back()->with('error', 'Solo se pueden aprobar solicitudes pendientes.');
        }

        $request->validate([
            'sol_obs_supervisor' => ['nullable', 'string', 'max:1000'],
        ]);

        $solicitud->update([
            'sol_estado'         => Solicitud::APROBADA,
            'sol_supervisor_id'  => Auth::id(),
            'sol_obs_supervisor' => $request->input('sol_obs_supervisor'),
        ]);

        return redirect()->route('solicitudes.show', $solicitud)
                         ->with('status', 'Solicitud aprobada.');
    }

    // ── RECHAZAR (pendiente → rechazada) ─────────────────────────────────────
    public function rechazar(Request $request, Solicitud $solicitud): RedirectResponse
    {
        if (! Auth::user()->tieneRol([2, 4])) abort(403);
        if (! $solicitud->esPendiente()) {
            return back()->with('error', 'Solo se pueden rechazar solicitudes pendientes.');
        }

        $request->validate([
            'sol_obs_supervisor' => ['required', 'string', 'max:1000'],
        ], [
            'sol_obs_supervisor.required' => 'Ingresá el motivo del rechazo.',
        ]);

        $solicitud->update([
            'sol_estado'         => Solicitud::RECHAZADA,
            'sol_supervisor_id'  => Auth::id(),
            'sol_obs_supervisor' => $request->input('sol_obs_supervisor'),
        ]);

        return redirect()->route('solicitudes.show', $solicitud)
                         ->with('status', 'Solicitud rechazada.');
    }

    // ── TOMAR (aprobada → en_proceso) ────────────────────────────────────────
    public function tomar(Solicitud $solicitud): RedirectResponse
    {
        if (! Auth::user()->tieneRol([3, 4])) abort(403);
        if (! $solicitud->esAprobada()) {
            return back()->with('error', 'Solo se pueden tomar solicitudes aprobadas.');
        }

        $solicitud->update([
            'sol_estado'      => Solicitud::EN_PROCESO,
            'sol_operador_id' => Auth::id(),
        ]);

        return redirect()->route('solicitudes.show', $solicitud)
                         ->with('status', 'Solicitud tomada. Queda en proceso.');
    }

    // ── COMPLETAR (en_proceso → completada) ──────────────────────────────────
    public function completar(Request $request, Solicitud $solicitud): RedirectResponse
    {
        $usuario = Auth::user();

        if (! $usuario->tieneRol([3, 4])) abort(403);
        if (! $solicitud->esEnProceso()) {
            return back()->with('error', 'Solo se pueden completar solicitudes en proceso.');
        }
        // Un operador solo puede completar la solicitud que él tomó (a menos que sea admin)
        if ($usuario->esOperador() && $solicitud->sol_operador_id !== $usuario->usr_id) {
            abort(403);
        }

        $request->validate([
            'sol_obs_operador' => ['nullable', 'string', 'max:1000'],
        ]);

        $solicitud->update([
            'sol_estado'       => Solicitud::COMPLETADA,
            'sol_obs_operador' => $request->input('sol_obs_operador'),
        ]);

        return redirect()->route('solicitudes.show', $solicitud)
                         ->with('status', 'Solicitud completada.');
    }

    // ── CANCELAR (borrador|pendiente → cancelada) ─────────────────────────────
    public function cancelar(Solicitud $solicitud): RedirectResponse
    {
        $usuario = Auth::user();

        if (! $usuario->tieneRol([1, 4])) abort(403);
        if ($solicitud->sol_usr_id !== $usuario->usr_id && ! $usuario->esAdmin()) abort(403);

        if (! in_array($solicitud->sol_estado, [Solicitud::BORRADOR, Solicitud::PENDIENTE])) {
            return back()->with('error', 'No se puede cancelar una solicitud en este estado.');
        }

        $solicitud->update(['sol_estado' => Solicitud::CANCELADA]);

        return redirect()->route('solicitudes.show', $solicitud)
                         ->with('status', 'Solicitud cancelada.');
    }

    // ── Helpers privados ─────────────────────────────────────────────────────
    private function puedeEditar($usuario, Solicitud $solicitud): bool
    {
        if ($usuario->esAdmin()) return true;

        return $usuario->tieneRol([1])
            && $solicitud->sol_usr_id === $usuario->usr_id
            && $solicitud->esBorrador();
    }
}
