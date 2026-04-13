<?php

namespace App\Http\Controllers;

use App\Models\AreaDest;
use App\Models\Comentario;
use App\Models\EstadoSol;
use App\Models\HistorialEstado;
use App\Models\Solicitud;
use App\Models\TipoSol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SolicitudController extends Controller
{
    // ── INDEX ────────────────────────────────────────────────────────────────
    public function index(Request $request): View
    {
        $usuario = Auth::user();
        $estadoFiltro = $request->query('estado');

        $query = Solicitud::with(['tipo', 'area', 'estado', 'solicitante'])
                          ->orderBy('sol_fecha_creacion', 'desc');

        if ($usuario->esSolicitante()) {
            $query->delUsuario($usuario->usr_id);
        } elseif ($usuario->esSupervisor()) {
            $query->enEstado([
                Solicitud::EST_ENVIADA, Solicitud::EST_EN_REVISION,
                Solicitud::EST_APROBADA, Solicitud::EST_RECHAZADA,
            ]);
        } elseif ($usuario->esOperador()) {
            $query->paraOperador();
        }

        if ($estadoFiltro) {
            $query->enEstado((int) $estadoFiltro);
        }

        $solicitudes = $query->paginate(20)->withQueryString();
        $estados     = EstadoSol::orderBy('est_orden')->get();

        return view('solicitudes.index', compact('solicitudes', 'estados', 'estadoFiltro'));
    }

    // ── CREATE ───────────────────────────────────────────────────────────────
    public function create(): View|RedirectResponse
    {
        if (! Auth::user()->tieneRol([1, 4])) abort(403);

        $tipos       = TipoSol::all();
        $areas       = AreaDest::all();
        $supervisores = User::where('usr_rol_id', 2)->activos()->get();

        return view('solicitudes.create', compact('tipos', 'areas', 'supervisores'));
    }

    // ── STORE ────────────────────────────────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        if (! Auth::user()->tieneRol([1, 4])) abort(403);

        $datos = $request->validate([
            'sol_tipo_id'      => ['required', 'integer'],
            'sol_area_dest_id' => ['required', 'integer'],
            'sol_titulo'       => ['required', 'string', 'max:200'],
            'sol_descripcion'  => ['required', 'string', 'min:10', 'max:4000'],
            'sol_prioridad'    => ['required', 'integer', 'in:1,2,3'],
            'sol_usr_supervisor'=>['required', 'integer'],
            'sol_sistema'      => ['nullable', 'string', 'max:100'],
            'enviar'           => ['nullable'],
        ], [
            'sol_tipo_id.required'      => 'Seleccioná el tipo de solicitud.',
            'sol_area_dest_id.required' => 'Seleccioná el área destino.',
            'sol_titulo.required'       => 'El título es obligatorio.',
            'sol_titulo.max'            => 'El título no puede superar 200 caracteres.',
            'sol_descripcion.required'  => 'La descripción es obligatoria.',
            'sol_descripcion.min'       => 'La descripción debe tener al menos 10 caracteres.',
            'sol_prioridad.required'    => 'Seleccioná la prioridad.',
            'sol_usr_supervisor.required'=>'Seleccioná el supervisor.',
        ]);

        $estadoId = $request->filled('enviar') ? Solicitud::EST_ENVIADA : Solicitud::EST_BORRADOR;

        $solicitud = DB::transaction(function () use ($datos, $estadoId) {
            $sol = Solicitud::create([
                'sol_numero'        => 'PENDIENTE',
                'sol_tipo_id'       => $datos['sol_tipo_id'],
                'sol_area_dest_id'  => $datos['sol_area_dest_id'],
                'sol_titulo'        => $datos['sol_titulo'],
                'sol_descripcion'   => $datos['sol_descripcion'],
                'sol_prioridad'     => $datos['sol_prioridad'],
                'sol_estado_id'     => $estadoId,
                'sol_sistema'       => $datos['sol_sistema'] ?? null,
                'sol_usr_solicita'  => Auth::id(),
                'sol_usr_supervisor'=> $datos['sol_usr_supervisor'],
                'sol_fecha_envio'   => $estadoId === Solicitud::EST_ENVIADA ? now() : null,
            ]);

            // Generar número legible usando el ID obtenido
            $sol->sol_numero = Solicitud::generarNumero($sol->sol_id);
            $sol->save();

            // Registrar en historial
            $this->registrarHistorial($sol, null, $estadoId, null);

            return $sol;
        });

        $msg = $estadoId === Solicitud::EST_ENVIADA
            ? 'Solicitud enviada correctamente.'
            : 'Solicitud guardada como borrador.';

        return redirect()->route('solicitudes.show', $solicitud)->with('status', $msg);
    }

    // ── SHOW ─────────────────────────────────────────────────────────────────
    public function show(Solicitud $solicitud): View
    {
        $usuario = Auth::user();

        if ($usuario->esSolicitante() && $solicitud->sol_usr_solicita !== $usuario->usr_id) {
            abort(403);
        }

        $solicitud->load(['tipo', 'area', 'estado', 'solicitante', 'supervisor', 'asignado',
                          'historial.usuario', 'historial.estadoAnterior', 'historial.estadoNuevo',
                          'comentarios.usuario']);

        $operadores = $usuario->esAdmin() || $usuario->esSupervisor()
            ? User::where('usr_rol_id', 3)->activos()->get()
            : collect();

        return view('solicitudes.show', compact('solicitud', 'operadores'));
    }

    // ── EDIT ──────────────────────────────────────────────────────────────────
    public function edit(Solicitud $solicitud): View|RedirectResponse
    {
        if (! $this->puedeEditar(Auth::user(), $solicitud)) abort(403);

        $tipos        = TipoSol::all();
        $areas        = AreaDest::all();
        $supervisores = User::where('usr_rol_id', 2)->activos()->get();

        return view('solicitudes.edit', compact('solicitud', 'tipos', 'areas', 'supervisores'));
    }

    // ── UPDATE ────────────────────────────────────────────────────────────────
    public function update(Request $request, Solicitud $solicitud): RedirectResponse
    {
        if (! $this->puedeEditar(Auth::user(), $solicitud)) abort(403);

        $datos = $request->validate([
            'sol_tipo_id'      => ['required', 'integer'],
            'sol_area_dest_id' => ['required', 'integer'],
            'sol_titulo'       => ['required', 'string', 'max:200'],
            'sol_descripcion'  => ['required', 'string', 'min:10', 'max:4000'],
            'sol_prioridad'    => ['required', 'integer', 'in:1,2,3'],
            'sol_usr_supervisor'=>['required', 'integer'],
            'sol_sistema'      => ['nullable', 'string', 'max:100'],
        ]);

        $solicitud->update($datos);

        return redirect()->route('solicitudes.show', $solicitud)->with('status', 'Solicitud actualizada.');
    }

    // ── DESTROY ───────────────────────────────────────────────────────────────
    public function destroy(Solicitud $solicitud): RedirectResponse
    {
        if (! $this->puedeEditar(Auth::user(), $solicitud)) abort(403);
        $solicitud->delete();
        return redirect()->route('solicitudes.index')->with('status', 'Solicitud eliminada.');
    }

    // ── ENVIAR (borrador → enviada) ───────────────────────────────────────────
    public function enviar(Solicitud $solicitud): RedirectResponse
    {
        $usuario = Auth::user();
        if (! $usuario->tieneRol([1, 4])) abort(403);
        if ($solicitud->sol_usr_solicita !== $usuario->usr_id && ! $usuario->esAdmin()) abort(403);
        if (! $solicitud->esBorrador()) return back()->with('error', 'Solo se pueden enviar borradores.');

        DB::transaction(function () use ($solicitud) {
            $anterior = $solicitud->sol_estado_id;
            $solicitud->update([
                'sol_estado_id'   => Solicitud::EST_ENVIADA,
                'sol_fecha_envio' => now(),
            ]);
            $this->registrarHistorial($solicitud, $anterior, Solicitud::EST_ENVIADA, null);
        });

        return redirect()->route('solicitudes.show', $solicitud)->with('status', 'Solicitud enviada.');
    }

    // ── TOMAR_REVISION (enviada → en_revision) ────────────────────────────────
    public function tomarRevision(Solicitud $solicitud): RedirectResponse
    {
        if (! Auth::user()->tieneRol([2, 4])) abort(403);
        if (! $solicitud->esEnviada()) return back()->with('error', 'La solicitud debe estar en estado Enviada.');

        DB::transaction(function () use ($solicitud) {
            $anterior = $solicitud->sol_estado_id;
            $solicitud->update(['sol_estado_id' => Solicitud::EST_EN_REVISION]);
            $this->registrarHistorial($solicitud, $anterior, Solicitud::EST_EN_REVISION, null);
        });

        return redirect()->route('solicitudes.show', $solicitud)->with('status', 'Solicitud tomada para revisión.');
    }

    // ── APROBAR (en_revision → aprobada) ──────────────────────────────────────
    public function aprobar(Request $request, Solicitud $solicitud): RedirectResponse
    {
        if (! Auth::user()->tieneRol([2, 4])) abort(403);
        if (! in_array($solicitud->sol_estado_id, [Solicitud::EST_ENVIADA, Solicitud::EST_EN_REVISION])) {
            return back()->with('error', 'Solo se pueden aprobar solicitudes enviadas o en revisión.');
        }

        $request->validate(['comentario' => ['nullable', 'string', 'max:1000']]);

        DB::transaction(function () use ($solicitud, $request) {
            $anterior = $solicitud->sol_estado_id;
            $solicitud->update([
                'sol_estado_id'        => Solicitud::EST_APROBADA,
                'sol_fecha_aprobacion' => now(),
            ]);
            $this->registrarHistorial($solicitud, $anterior, Solicitud::EST_APROBADA, $request->comentario);
        });

        return redirect()->route('solicitudes.show', $solicitud)->with('status', 'Solicitud aprobada.');
    }

    // ── RECHAZAR (enviada|en_revision → rechazada) ────────────────────────────
    public function rechazar(Request $request, Solicitud $solicitud): RedirectResponse
    {
        if (! Auth::user()->tieneRol([2, 4])) abort(403);
        if (! in_array($solicitud->sol_estado_id, [Solicitud::EST_ENVIADA, Solicitud::EST_EN_REVISION])) {
            return back()->with('error', 'Solo se pueden rechazar solicitudes enviadas o en revisión.');
        }

        $request->validate([
            'comentario' => ['required', 'string', 'max:1000'],
        ], ['comentario.required' => 'Ingresá el motivo del rechazo.']);

        DB::transaction(function () use ($solicitud, $request) {
            $anterior = $solicitud->sol_estado_id;
            $solicitud->update(['sol_estado_id' => Solicitud::EST_RECHAZADA]);
            $this->registrarHistorial($solicitud, $anterior, Solicitud::EST_RECHAZADA, $request->comentario);
        });

        return redirect()->route('solicitudes.show', $solicitud)->with('status', 'Solicitud rechazada.');
    }

    // ── ASIGNAR (aprobada → asignada) ─────────────────────────────────────────
    public function asignar(Request $request, Solicitud $solicitud): RedirectResponse
    {
        if (! Auth::user()->tieneRol([2, 3, 4])) abort(403);
        if (! $solicitud->esAprobada()) return back()->with('error', 'Solo se pueden asignar solicitudes aprobadas.');

        $request->validate([
            'sol_usr_asignado' => ['required', 'integer'],
            'comentario'       => ['nullable', 'string', 'max:1000'],
        ], ['sol_usr_asignado.required' => 'Seleccioná el operador a asignar.']);

        DB::transaction(function () use ($solicitud, $request) {
            $anterior = $solicitud->sol_estado_id;
            $solicitud->update([
                'sol_estado_id'    => Solicitud::EST_ASIGNADA,
                'sol_usr_asignado' => $request->sol_usr_asignado,
            ]);
            $this->registrarHistorial($solicitud, $anterior, Solicitud::EST_ASIGNADA, $request->comentario);
        });

        return redirect()->route('solicitudes.show', $solicitud)->with('status', 'Solicitud asignada.');
    }

    // ── INICIAR (asignada → en_progreso) ──────────────────────────────────────
    public function iniciar(Solicitud $solicitud): RedirectResponse
    {
        $usuario = Auth::user();
        if (! $usuario->tieneRol([3, 4])) abort(403);
        if ($usuario->esOperador() && $solicitud->sol_usr_asignado !== $usuario->usr_id) abort(403);
        if (! $solicitud->esAsignada()) return back()->with('error', 'La solicitud debe estar asignada.');

        DB::transaction(function () use ($solicitud) {
            $anterior = $solicitud->sol_estado_id;
            $solicitud->update(['sol_estado_id' => Solicitud::EST_EN_PROGRESO]);
            $this->registrarHistorial($solicitud, $anterior, Solicitud::EST_EN_PROGRESO, null);
        });

        return redirect()->route('solicitudes.show', $solicitud)->with('status', 'Solicitud iniciada.');
    }

    // ── RESOLVER (en_progreso → resuelta) ────────────────────────────────────
    public function resolver(Request $request, Solicitud $solicitud): RedirectResponse
    {
        $usuario = Auth::user();
        if (! $usuario->tieneRol([3, 4])) abort(403);
        if ($usuario->esOperador() && $solicitud->sol_usr_asignado !== $usuario->usr_id) abort(403);
        if (! $solicitud->esEnProgreso()) return back()->with('error', 'La solicitud debe estar en progreso.');

        $request->validate([
            'sol_resolucion' => ['required', 'string', 'max:4000'],
        ], ['sol_resolucion.required' => 'Describí cómo fue resuelta la solicitud.']);

        DB::transaction(function () use ($solicitud, $request) {
            $anterior = $solicitud->sol_estado_id;
            $solicitud->update([
                'sol_estado_id'  => Solicitud::EST_RESUELTA,
                'sol_resolucion' => $request->sol_resolucion,
            ]);
            $this->registrarHistorial($solicitud, $anterior, Solicitud::EST_RESUELTA, $request->sol_resolucion);
        });

        return redirect()->route('solicitudes.show', $solicitud)->with('status', 'Solicitud marcada como resuelta.');
    }

    // ── CERRAR (resuelta → cerrada) ───────────────────────────────────────────
    public function cerrar(Request $request, Solicitud $solicitud): RedirectResponse
    {
        $usuario = Auth::user();
        if (! $usuario->tieneRol([1, 2, 4])) abort(403);
        if ($usuario->esSolicitante() && $solicitud->sol_usr_solicita !== $usuario->usr_id) abort(403);
        if (! $solicitud->esResuelta()) return back()->with('error', 'Solo se pueden cerrar solicitudes resueltas.');

        DB::transaction(function () use ($solicitud, $request) {
            $anterior = $solicitud->sol_estado_id;
            $solicitud->update([
                'sol_estado_id'    => Solicitud::EST_CERRADA,
                'sol_fecha_cierre' => now(),
            ]);
            $this->registrarHistorial($solicitud, $anterior, Solicitud::EST_CERRADA, $request->comentario ?? null);
        });

        return redirect()->route('solicitudes.show', $solicitud)->with('status', 'Solicitud cerrada.');
    }

    // ── COMENTAR ──────────────────────────────────────────────────────────────
    public function comentar(Request $request, Solicitud $solicitud): RedirectResponse
    {
        $usuario = Auth::user();

        if ($usuario->esSolicitante() && $solicitud->sol_usr_solicita !== $usuario->usr_id) abort(403);

        $request->validate([
            'com_texto'   => ['required', 'string', 'max:2000'],
            'com_interno' => ['nullable'],
        ], ['com_texto.required' => 'El comentario no puede estar vacío.']);

        Comentario::create([
            'com_sol_id'  => $solicitud->sol_id,
            'com_usr_id'  => $usuario->usr_id,
            'com_texto'   => $request->com_texto,
            'com_interno' => ($request->has('com_interno') && $usuario->tieneRol([2, 3, 4])) ? 1 : 0,
            'com_fecha'   => now(),
        ]);

        return redirect()->route('solicitudes.show', $solicitud)
                         ->with('status', 'Comentario agregado.')
                         ->withFragment('comentarios');
    }

    // ── Helpers privados ──────────────────────────────────────────────────────
    private function puedeEditar($usuario, Solicitud $solicitud): bool
    {
        if ($usuario->esAdmin()) return true;
        return $usuario->tieneRol([1])
            && $solicitud->sol_usr_solicita === $usuario->usr_id
            && $solicitud->esBorrador();
    }

    private function registrarHistorial(
        Solicitud $solicitud,
        ?int $anterior,
        int $nuevo,
        ?string $comentario
    ): void {
        HistorialEstado::create([
            'his_sol_id'       => $solicitud->sol_id,
            'his_est_anterior' => $anterior,
            'his_est_nuevo'    => $nuevo,
            'his_usr_id'       => Auth::id(),
            'his_comentario'   => $comentario,
            'his_fecha'        => now(),
        ]);
    }
}
