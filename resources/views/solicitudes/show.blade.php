@extends('layouts.app')

@section('title', $solicitud->numero)

@push('styles')
<style>
    .page-header {
        display: flex; align-items: flex-start; justify-content: space-between;
        margin-bottom: 20px; gap: 12px; flex-wrap: wrap;
    }
    .page-header h1 { font-size: 20px; color: #1a3a5c; margin-bottom: 4px; }
    .page-header .meta { font-size: 12px; color: #888; }

    .badge-estado {
        display: inline-block;
        padding: 3px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        color: #fff;
    }

    /* Layout en dos columnas */
    .layout { display: grid; grid-template-columns: 1fr 340px; gap: 20px; align-items: start; }
    @media (max-width: 760px) { .layout { grid-template-columns: 1fr; } }

    .card {
        background: #fff;
        border-radius: 6px;
        box-shadow: 0 1px 4px rgba(0,0,0,.08);
        padding: 24px;
        margin-bottom: 16px;
    }
    .card h3 {
        font-size: 11px; font-weight: 600; text-transform: uppercase;
        letter-spacing: .5px; color: #888; margin-bottom: 14px;
    }

    .campo { margin-bottom: 14px; }
    .campo .etiqueta { font-size: 11px; color: #999; text-transform: uppercase; letter-spacing: .4px; margin-bottom: 2px; }
    .campo .valor { font-size: 14px; color: #333; }
    .campo .descripcion { font-size: 14px; color: #333; line-height: 1.6; white-space: pre-wrap; word-break: break-word; }

    .timeline { list-style: none; position: relative; padding-left: 20px; }
    .timeline::before {
        content: ''; position: absolute; left: 6px; top: 4px; bottom: 4px;
        width: 2px; background: #e8eaed;
    }
    .timeline li {
        position: relative; padding: 0 0 16px 16px; font-size: 13px;
    }
    .timeline li::before {
        content: ''; position: absolute; left: -2px; top: 5px;
        width: 10px; height: 10px; border-radius: 50%;
        background: #2e6da4; border: 2px solid #fff; box-shadow: 0 0 0 2px #e8eaed;
    }
    .timeline li:last-child { padding-bottom: 0; }
    .timeline .tl-fecha { font-size: 11px; color: #aaa; margin-top: 2px; }

    /* Acciones */
    .acciones { display: flex; flex-direction: column; gap: 10px; }
    .btn {
        display: block; width: 100%; padding: 9px 16px; border-radius: 4px;
        font-size: 13px; font-family: inherit; cursor: pointer; border: none;
        text-align: center; text-decoration: none;
    }
    .btn-primary   { background: #1a3a5c; color: #fff; }
    .btn-primary:hover { background: #24507e; }
    .btn-success   { background: #27ae60; color: #fff; }
    .btn-success:hover { background: #219a52; }
    .btn-warning   { background: #e67e22; color: #fff; }
    .btn-warning:hover { background: #d35400; }
    .btn-danger    { background: #c0392b; color: #fff; }
    .btn-danger:hover { background: #a93226; }
    .btn-purple    { background: #8e44ad; color: #fff; }
    .btn-purple:hover { background: #7d3c98; }
    .btn-outline   { background: transparent; border: 1px solid #ccc; color: #555; }
    .btn-outline:hover { background: #f0f2f5; }

    /* Formulario inline de obs */
    .obs-form { margin-top: 10px; }
    .obs-form textarea {
        width: 100%; padding: 7px 9px; border: 1px solid #d0d5dd;
        border-radius: 4px; font-size: 13px; font-family: inherit;
        resize: vertical; min-height: 70px; margin-bottom: 8px;
    }
    .obs-form textarea:focus { outline: none; border-color: #2e6da4; }
    .obs-form .error { font-size: 12px; color: #c0392b; margin-bottom: 6px; }
    .obs-form label { font-size: 12px; color: #666; margin-bottom: 4px; display: block; }

    .divider { border: none; border-top: 1px solid #f0f2f5; margin: 12px 0; }

    .badge-prioridad {
        display: inline-block; padding: 2px 8px;
        border-radius: 4px; font-size: 11px; font-weight: 600;
    }
</style>
@endpush

@section('content')

@php $usuario = Auth::user(); @endphp

<div style="margin-bottom: 16px;">
    <a href="{{ route('solicitudes.index') }}"
       style="font-size:13px; color:#2e6da4; text-decoration:none;">← Volver a solicitudes</a>
</div>

<div class="page-header">
    <div>
        <h1>{{ $solicitud->numero }}</h1>
        <div class="meta">
            Creada el {{ $solicitud->sol_creado ? $solicitud->sol_creado->format('d/m/Y H:i') : '—' }}
            por {{ $solicitud->solicitante->usr_nombre ?? '—' }}
        </div>
    </div>
    <span class="badge-estado" style="background:{{ $solicitud->estado_color }}; margin-top: 4px;">
        {{ $solicitud->estado_label }}
    </span>
</div>

<div class="layout">

    {{-- Columna izquierda: detalle --}}
    <div>
        <div class="card">
            <h3>Detalle</h3>

            <div class="campo">
                <div class="etiqueta">Tipo</div>
                <div class="valor">{{ $solicitud->tipo_label }}</div>
            </div>

            <div class="campo">
                <div class="etiqueta">Prioridad</div>
                <div class="valor">
                    <span class="badge-prioridad"
                          style="color:{{ $solicitud->prioridad_color }}; background:{{ $solicitud->prioridad_color }}22;">
                        {{ $solicitud->prioridad_label }}
                    </span>
                </div>
            </div>

            <div class="campo">
                <div class="etiqueta">Descripción</div>
                <div class="descripcion">{{ $solicitud->sol_descripcion }}</div>
            </div>
        </div>

        {{-- Revisión del supervisor --}}
        @if($solicitud->sol_supervisor_id)
        <div class="card">
            <h3>Revisión del supervisor</h3>
            <div class="campo">
                <div class="etiqueta">Supervisor</div>
                <div class="valor">{{ $solicitud->supervisor->usr_nombre ?? '—' }}</div>
            </div>
            @if($solicitud->sol_obs_supervisor)
            <div class="campo">
                <div class="etiqueta">Observaciones</div>
                <div class="descripcion">{{ $solicitud->sol_obs_supervisor }}</div>
            </div>
            @endif
        </div>
        @endif

        {{-- Procesamiento del operador --}}
        @if($solicitud->sol_operador_id)
        <div class="card">
            <h3>Procesamiento</h3>
            <div class="campo">
                <div class="etiqueta">Operador</div>
                <div class="valor">{{ $solicitud->operador->usr_nombre ?? '—' }}</div>
            </div>
            @if($solicitud->sol_obs_operador)
            <div class="campo">
                <div class="etiqueta">Observaciones</div>
                <div class="descripcion">{{ $solicitud->sol_obs_operador }}</div>
            </div>
            @endif
        </div>
        @endif

        {{-- Historial / línea de tiempo --}}
        <div class="card">
            <h3>Historial</h3>
            <ul class="timeline">
                <li>
                    <strong>Solicitud creada</strong>
                    <div class="tl-fecha">
                        {{ $solicitud->sol_creado ? $solicitud->sol_creado->format('d/m/Y H:i') : '—' }}
                        · {{ $solicitud->solicitante->usr_nombre ?? '' }}
                    </div>
                </li>
                @if(!$solicitud->esBorrador())
                <li>
                    <strong>Enviada para revisión</strong>
                    <div class="tl-fecha">Estado: Pendiente</div>
                </li>
                @endif
                @if($solicitud->esAprobada() || $solicitud->esEnProceso() || $solicitud->esCompletada())
                <li>
                    <strong>Aprobada</strong>
                    <div class="tl-fecha">{{ $solicitud->supervisor->usr_nombre ?? '' }}</div>
                </li>
                @endif
                @if($solicitud->esRechazada())
                <li>
                    <strong style="color:#c0392b;">Rechazada</strong>
                    <div class="tl-fecha">{{ $solicitud->supervisor->usr_nombre ?? '' }}</div>
                </li>
                @endif
                @if($solicitud->esEnProceso() || $solicitud->esCompletada())
                <li>
                    <strong>Tomada por operador</strong>
                    <div class="tl-fecha">{{ $solicitud->operador->usr_nombre ?? '' }}</div>
                </li>
                @endif
                @if($solicitud->esCompletada())
                <li>
                    <strong style="color:#27ae60;">Completada</strong>
                    <div class="tl-fecha">
                        {{ $solicitud->sol_actualizado ? $solicitud->sol_actualizado->format('d/m/Y H:i') : '' }}
                    </div>
                </li>
                @endif
                @if($solicitud->esCancelada())
                <li>
                    <strong style="color:#95a5a6;">Cancelada</strong>
                </li>
                @endif
            </ul>
        </div>
    </div>

    {{-- Columna derecha: acciones según rol y estado --}}
    <div>
        @if(!$solicitud->estaTerminada())
        <div class="card">
            <h3>Acciones</h3>
            <div class="acciones">

                {{-- Solicitante: borrador --}}
                @if($usuario->tieneRol([1,4]) && $solicitud->sol_usr_id === $usuario->usr_id && $solicitud->esBorrador())
                    <a href="{{ route('solicitudes.edit', $solicitud) }}" class="btn btn-outline">Editar</a>

                    <form method="POST" action="{{ route('solicitudes.enviar', $solicitud) }}">
                        @csrf
                        <button type="submit" class="btn btn-success">Enviar para revisión</button>
                    </form>

                    <hr class="divider">

                    <form method="POST" action="{{ route('solicitudes.cancelar', $solicitud) }}"
                          onsubmit="return confirm('¿Cancelar esta solicitud?')">
                        @csrf
                        <button type="submit" class="btn btn-outline" style="color:#c0392b; border-color:#f5c6cb;">
                            Cancelar solicitud
                        </button>
                    </form>
                @endif

                {{-- Solicitante: pendiente → puede cancelar --}}
                @if($usuario->tieneRol([1]) && $solicitud->sol_usr_id === $usuario->usr_id && $solicitud->esPendiente())
                    <form method="POST" action="{{ route('solicitudes.cancelar', $solicitud) }}"
                          onsubmit="return confirm('¿Cancelar esta solicitud?')">
                        @csrf
                        <button type="submit" class="btn btn-outline" style="color:#c0392b; border-color:#f5c6cb;">
                            Cancelar solicitud
                        </button>
                    </form>
                @endif

                {{-- Supervisor: pendiente → aprobar / rechazar --}}
                @if($usuario->tieneRol([2,4]) && $solicitud->esPendiente())

                    <form method="POST" action="{{ route('solicitudes.aprobar', $solicitud) }}" class="obs-form">
                        @csrf
                        <label>Observaciones (opcional)</label>
                        <textarea name="sol_obs_supervisor"
                                  placeholder="Podés agregar una nota...">{{ old('sol_obs_supervisor') }}</textarea>
                        @error('sol_obs_supervisor')
                            <div class="error">{{ $message }}</div>
                        @enderror
                        <button type="submit" class="btn btn-success">Aprobar</button>
                    </form>

                    <hr class="divider">

                    <form method="POST" action="{{ route('solicitudes.rechazar', $solicitud) }}" class="obs-form">
                        @csrf
                        <label>Motivo del rechazo <span style="color:#c0392b">*</span></label>
                        <textarea name="sol_obs_supervisor"
                                  placeholder="Ingresá el motivo...">{{ old('sol_obs_supervisor') }}</textarea>
                        @error('sol_obs_supervisor')
                            <div class="error">{{ $message }}</div>
                        @enderror
                        <button type="submit" class="btn btn-danger">Rechazar</button>
                    </form>
                @endif

                {{-- Operador: aprobada → tomar --}}
                @if($usuario->tieneRol([3,4]) && $solicitud->esAprobada())
                    <form method="POST" action="{{ route('solicitudes.tomar', $solicitud) }}">
                        @csrf
                        <button type="submit" class="btn btn-purple">Tomar solicitud</button>
                    </form>
                @endif

                {{-- Operador: en_proceso → completar --}}
                @if($usuario->tieneRol([3,4]) && $solicitud->esEnProceso()
                    && ($usuario->esAdmin() || $solicitud->sol_operador_id === $usuario->usr_id))

                    <form method="POST" action="{{ route('solicitudes.completar', $solicitud) }}" class="obs-form">
                        @csrf
                        <label>Observaciones (opcional)</label>
                        <textarea name="sol_obs_operador"
                                  placeholder="Detallá el trabajo realizado...">{{ old('sol_obs_operador') }}</textarea>
                        @error('sol_obs_operador')
                            <div class="error">{{ $message }}</div>
                        @enderror
                        <button type="submit" class="btn btn-success">Marcar como completada</button>
                    </form>
                @endif

                {{-- Admin: cancelar en cualquier estado activo --}}
                @if($usuario->esAdmin() && in_array($solicitud->sol_estado, ['borrador','pendiente']))
                    <hr class="divider">
                    <form method="POST" action="{{ route('solicitudes.cancelar', $solicitud) }}"
                          onsubmit="return confirm('¿Cancelar esta solicitud?')">
                        @csrf
                        <button type="submit" class="btn btn-outline" style="color:#c0392b; border-color:#f5c6cb;">
                            Cancelar solicitud
                        </button>
                    </form>
                @endif

            </div>
        </div>
        @endif

        {{-- Eliminar (solo borrador, propietario o admin) --}}
        @if($solicitud->esBorrador() && ($usuario->esAdmin() || $solicitud->sol_usr_id === $usuario->usr_id))
        <div class="card">
            <h3>Zona peligrosa</h3>
            <form method="POST" action="{{ route('solicitudes.destroy', $solicitud) }}"
                  onsubmit="return confirm('¿Eliminar permanentemente esta solicitud?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" style="display:block; width:100%;">
                    Eliminar solicitud
                </button>
            </form>
        </div>
        @endif
    </div>

</div>

@endsection
