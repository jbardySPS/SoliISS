@extends('layouts.app')

@section('title', $solicitud->sol_numero)

@push('styles')
<style>
    .page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:20px; gap:12px; flex-wrap:wrap; }
    .page-header h1 { font-size:20px; color:#1a3a5c; margin-bottom:4px; }
    .page-header .meta { font-size:12px; color:#888; }

    .layout { display:grid; grid-template-columns:1fr 320px; gap:20px; align-items:start; }
    @media(max-width:760px){ .layout { grid-template-columns:1fr; } }

    .card { background:#fff; border-radius:6px; box-shadow:0 1px 4px rgba(0,0,0,.08); padding:24px; margin-bottom:16px; }
    .card h3 { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.5px; color:#888; margin-bottom:14px; }

    .campo { margin-bottom:14px; }
    .campo .etq { font-size:11px; color:#999; text-transform:uppercase; letter-spacing:.4px; margin-bottom:2px; }
    .campo .val { font-size:14px; color:#333; }
    .campo .desc { font-size:14px; color:#333; line-height:1.6; white-space:pre-wrap; word-break:break-word; }

    .badge { display:inline-block; padding:3px 12px; border-radius:20px; font-size:12px; font-weight:600; color:#fff; }
    .badge-prio { display:inline-block; padding:2px 8px; border-radius:4px; font-size:12px; font-weight:600; }

    /* Timeline de historial */
    .timeline { list-style:none; position:relative; padding-left:20px; }
    .timeline::before { content:''; position:absolute; left:6px; top:4px; bottom:4px; width:2px; background:#e8eaed; }
    .timeline li { position:relative; padding:0 0 16px 16px; font-size:13px; }
    .timeline li::before { content:''; position:absolute; left:-2px; top:5px; width:10px; height:10px; border-radius:50%; background:#2e6da4; border:2px solid #fff; box-shadow:0 0 0 2px #e8eaed; }
    .timeline li:last-child { padding-bottom:0; }
    .tl-fecha { font-size:11px; color:#aaa; margin-top:2px; }
    .tl-comentario { font-size:12px; color:#555; margin-top:4px; font-style:italic; background:#f8f9fa; padding:5px 8px; border-radius:3px; }

    /* Comentarios */
    .comentario { padding:12px 0; border-bottom:1px solid #f0f2f5; }
    .comentario:last-child { border-bottom:none; }
    .com-header { display:flex; justify-content:space-between; margin-bottom:4px; font-size:12px; }
    .com-autor { font-weight:600; color:#333; }
    .com-fecha { color:#aaa; }
    .com-texto { font-size:13px; color:#333; white-space:pre-wrap; }
    .com-interno { display:inline-block; font-size:10px; padding:1px 6px; border-radius:3px; background:#fff3cd; color:#856404; border:1px solid #ffc107; margin-left:6px; }

    /* Acciones */
    .btn { display:block; width:100%; padding:9px 16px; border-radius:4px; font-size:13px; font-family:inherit; cursor:pointer; border:none; text-align:center; text-decoration:none; margin-bottom:8px; }
    .btn:last-child { margin-bottom:0; }
    .btn-primary  { background:#1a3a5c; color:#fff; }
    .btn-primary:hover { background:#24507e; }
    .btn-success  { background:#27ae60; color:#fff; }
    .btn-success:hover { background:#219a52; }
    .btn-warning  { background:#e67e22; color:#fff; }
    .btn-warning:hover { background:#d35400; }
    .btn-danger   { background:#c0392b; color:#fff; }
    .btn-danger:hover { background:#a93226; }
    .btn-purple   { background:#8e44ad; color:#fff; }
    .btn-purple:hover { background:#7d3c98; }
    .btn-teal     { background:#16a085; color:#fff; }
    .btn-teal:hover { background:#138a72; }
    .btn-outline  { background:transparent; border:1px solid #ccc; color:#555; }
    .btn-outline:hover { background:#f0f2f5; }

    .obs-form { margin-top:10px; }
    .obs-form label { font-size:12px; color:#666; margin-bottom:4px; display:block; }
    .obs-form textarea, .obs-form select { width:100%; padding:7px 9px; border:1px solid #d0d5dd; border-radius:4px; font-size:13px; font-family:inherit; margin-bottom:8px; }
    .obs-form textarea { resize:vertical; min-height:70px; }
    .obs-form textarea:focus, .obs-form select:focus { outline:none; border-color:#2e6da4; }
    .obs-form .error { font-size:12px; color:#c0392b; margin-bottom:6px; }

    .divider { border:none; border-top:1px solid #f0f2f5; margin:12px 0; }

    .com-form textarea { width:100%; padding:8px 10px; border:1px solid #d0d5dd; border-radius:4px; font-size:13px; font-family:inherit; resize:vertical; min-height:80px; margin-bottom:8px; }
    .com-form textarea:focus { outline:none; border-color:#2e6da4; }
</style>
@endpush

@section('content')
@php $usuario = Auth::user(); $est = $solicitud->estado; @endphp

<div style="margin-bottom:16px;">
    <a href="{{ route('solicitudes.index') }}" style="font-size:13px; color:#2e6da4; text-decoration:none;">← Volver</a>
</div>

<div class="page-header">
    <div>
        <h1>{{ $solicitud->sol_titulo }}</h1>
        <div class="meta">
            {{ $solicitud->sol_numero }} ·
            Creada el {{ $solicitud->sol_fecha_creacion?->format('d/m/Y H:i') ?? '—' }}
            por {{ $solicitud->solicitante->usr_nombre ?? '—' }}
        </div>
    </div>
    @if($est)
        <span class="badge" style="background:{{ $est->est_color }}; margin-top:4px;">
            {{ $est->est_nombre }}
        </span>
    @endif
</div>

<div class="layout">
    {{-- Columna izquierda --}}
    <div>
        {{-- Detalle --}}
        <div class="card">
            <h3>Detalle</h3>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;">
                <div class="campo">
                    <div class="etq">Tipo</div>
                    <div class="val">{{ $solicitud->tipo->tipo_nombre ?? '—' }}</div>
                </div>
                <div class="campo">
                    <div class="etq">Área destino</div>
                    <div class="val">{{ $solicitud->area->area_nombre ?? '—' }}</div>
                </div>
                <div class="campo">
                    <div class="etq">Prioridad</div>
                    <div class="val">
                        <span class="badge-prio"
                              style="color:{{ $solicitud->prioridad_color }}; background:{{ $solicitud->prioridad_color }}22;">
                            {{ $solicitud->prioridad_label }}
                        </span>
                    </div>
                </div>
                @if($solicitud->sol_sistema)
                <div class="campo">
                    <div class="etq">Sistema</div>
                    <div class="val">{{ $solicitud->sol_sistema }}</div>
                </div>
                @endif
            </div>
            <div class="campo">
                <div class="etq">Descripción</div>
                <div class="desc">{{ $solicitud->sol_descripcion }}</div>
            </div>
        </div>

        {{-- Asignación --}}
        @if($solicitud->sol_usr_supervisor || $solicitud->sol_usr_asignado)
        <div class="card">
            <h3>Asignación</h3>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <div class="campo">
                    <div class="etq">Supervisor</div>
                    <div class="val">{{ $solicitud->supervisor->usr_nombre ?? '—' }}</div>
                </div>
                <div class="campo">
                    <div class="etq">Operador asignado</div>
                    <div class="val">{{ $solicitud->asignado->usr_nombre ?? 'Sin asignar' }}</div>
                </div>
            </div>
        </div>
        @endif

        {{-- Resolución --}}
        @if($solicitud->sol_resolucion)
        <div class="card">
            <h3>Resolución</h3>
            <div class="desc">{{ $solicitud->sol_resolucion }}</div>
        </div>
        @endif

        {{-- Historial --}}
        <div class="card">
            <h3>Historial de estados</h3>
            <ul class="timeline">
                @foreach($solicitud->historial as $h)
                <li>
                    <strong>{{ $h->estadoNuevo->est_nombre ?? '—' }}</strong>
                    @if($h->estadoAnterior)
                        <span style="color:#aaa; font-size:11px;"> (desde {{ $h->estadoAnterior->est_nombre }})</span>
                    @endif
                    <div class="tl-fecha">
                        {{ $h->his_fecha?->format('d/m/Y H:i') }} · {{ $h->usuario->usr_nombre ?? '—' }}
                    </div>
                    @if($h->his_comentario)
                        <div class="tl-comentario">{{ $h->his_comentario }}</div>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>

        {{-- Comentarios --}}
        <div class="card" id="comentarios">
            <h3>Comentarios ({{ $solicitud->comentarios->count() }})</h3>

            @forelse($solicitud->comentarios as $com)
            <div class="comentario">
                <div class="com-header">
                    <span class="com-autor">
                        {{ $com->usuario->usr_nombre ?? '—' }}
                        @if($com->com_interno)
                            <span class="com-interno">Interno</span>
                        @endif
                    </span>
                    <span class="com-fecha">{{ $com->com_fecha?->format('d/m/Y H:i') }}</span>
                </div>
                <div class="com-texto">{{ $com->com_texto }}</div>
            </div>
            @empty
            <p style="color:#aaa; font-size:13px;">Sin comentarios aún.</p>
            @endforelse

            {{-- Agregar comentario --}}
            <form method="POST" action="{{ route('solicitudes.comentar', $solicitud) }}" class="com-form" style="margin-top:16px;">
                @csrf
                <textarea name="com_texto" placeholder="Escribí un comentario..."
                          required>{{ old('com_texto') }}</textarea>
                @error('com_texto') <div style="font-size:12px; color:#c0392b; margin-bottom:6px;">{{ $message }}</div> @enderror

                <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                    <button type="submit" class="btn btn-outline"
                            style="width:auto; padding:6px 16px; display:inline-block;">
                        Agregar comentario
                    </button>
                    @if($usuario->tieneRol([2, 3, 4]))
                    <label style="font-size:12px; color:#555; display:flex; align-items:center; gap:5px;">
                        <input type="checkbox" name="com_interno" value="1"
                               {{ old('com_interno') ? 'checked' : '' }}>
                        Solo interno (no visible para el solicitante)
                    </label>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Columna derecha: acciones --}}
    <div>
        @if($solicitud->estaActiva())
        <div class="card">
            <h3>Acciones</h3>

            {{-- BORRADOR: solicitante puede editar, enviar, eliminar --}}
            @if($solicitud->esBorrador() && $usuario->tieneRol([1,4]) && ($solicitud->sol_usr_solicita === $usuario->usr_id || $usuario->esAdmin()))
                <a href="{{ route('solicitudes.edit', $solicitud) }}" class="btn btn-outline">Editar</a>

                <form method="POST" action="{{ route('solicitudes.enviar', $solicitud) }}">
                    @csrf
                    <button type="submit" class="btn btn-success">Enviar para revisión</button>
                </form>
            @endif

            {{-- ENVIADA: supervisor puede tomar o rechazar directo --}}
            @if($solicitud->esEnviada() && $usuario->tieneRol([2, 4]))
                <form method="POST" action="{{ route('solicitudes.tomarRevision', $solicitud) }}">
                    @csrf
                    <button type="submit" class="btn btn-warning">Tomar para revisión</button>
                </form>

                <hr class="divider">

                <form method="POST" action="{{ route('solicitudes.aprobar', $solicitud) }}" class="obs-form">
                    @csrf
                    <label>Observaciones (opcional)</label>
                    <textarea name="comentario" placeholder="Nota de aprobación..."></textarea>
                    <button type="submit" class="btn btn-success">Aprobar directamente</button>
                </form>

                <hr class="divider">

                <form method="POST" action="{{ route('solicitudes.rechazar', $solicitud) }}" class="obs-form">
                    @csrf
                    <label>Motivo del rechazo <span style="color:#c0392b">*</span></label>
                    <textarea name="comentario" placeholder="Motivo obligatorio..." required></textarea>
                    @error('comentario') <div class="error">{{ $message }}</div> @enderror
                    <button type="submit" class="btn btn-danger">Rechazar</button>
                </form>
            @endif

            {{-- EN REVISIÓN: supervisor aprueba o rechaza --}}
            @if($solicitud->esEnRevision() && $usuario->tieneRol([2, 4]))
                <form method="POST" action="{{ route('solicitudes.aprobar', $solicitud) }}" class="obs-form">
                    @csrf
                    <label>Observaciones (opcional)</label>
                    <textarea name="comentario" placeholder="Nota de aprobación..."></textarea>
                    <button type="submit" class="btn btn-success">Aprobar</button>
                </form>

                <hr class="divider">

                <form method="POST" action="{{ route('solicitudes.rechazar', $solicitud) }}" class="obs-form">
                    @csrf
                    <label>Motivo del rechazo <span style="color:#c0392b">*</span></label>
                    <textarea name="comentario" placeholder="Motivo obligatorio..." required></textarea>
                    @error('comentario') <div class="error">{{ $message }}</div> @enderror
                    <button type="submit" class="btn btn-danger">Rechazar</button>
                </form>
            @endif

            {{-- APROBADA: supervisor/admin asigna a operador --}}
            @if($solicitud->esAprobada() && $usuario->tieneRol([2, 3, 4]))
                <form method="POST" action="{{ route('solicitudes.asignar', $solicitud) }}" class="obs-form">
                    @csrf
                    <label>Asignar a operador <span style="color:#c0392b">*</span></label>
                    <select name="sol_usr_asignado" required>
                        <option value="">— Seleccioná —</option>
                        @foreach($operadores as $op)
                            <option value="{{ $op->usr_id }}">{{ $op->usr_nombre }}</option>
                        @endforeach
                    </select>
                    <label style="margin-top:4px;">Comentario (opcional)</label>
                    <textarea name="comentario" placeholder="Instrucciones para el operador..."></textarea>
                    <button type="submit" class="btn btn-purple">Asignar</button>
                </form>
            @endif

            {{-- ASIGNADA: operador inicia --}}
            @if($solicitud->esAsignada() && $usuario->tieneRol([3,4]) && ($usuario->esAdmin() || $solicitud->sol_usr_asignado === $usuario->usr_id))
                <form method="POST" action="{{ route('solicitudes.iniciar', $solicitud) }}">
                    @csrf
                    <button type="submit" class="btn btn-teal">Iniciar trabajo</button>
                </form>
            @endif

            {{-- EN PROGRESO: operador resuelve --}}
            @if($solicitud->esEnProgreso() && $usuario->tieneRol([3,4]) && ($usuario->esAdmin() || $solicitud->sol_usr_asignado === $usuario->usr_id))
                <form method="POST" action="{{ route('solicitudes.resolver', $solicitud) }}" class="obs-form">
                    @csrf
                    <label>Descripción de la resolución <span style="color:#c0392b">*</span></label>
                    <textarea name="sol_resolucion" placeholder="Describí cómo fue resuelta..." required></textarea>
                    @error('sol_resolucion') <div class="error">{{ $message }}</div> @enderror
                    <button type="submit" class="btn btn-success">Marcar como resuelta</button>
                </form>
            @endif

            {{-- RESUELTA: solicitante/supervisor confirma cierre --}}
            @if($solicitud->esResuelta() && $usuario->tieneRol([1,2,4]) && ($usuario->esAdmin() || $usuario->esSupervisor() || $solicitud->sol_usr_solicita === $usuario->usr_id))
                <form method="POST" action="{{ route('solicitudes.cerrar', $solicitud) }}" class="obs-form">
                    @csrf
                    <label>Comentario de cierre (opcional)</label>
                    <textarea name="comentario" placeholder="Confirmación o nota final..."></textarea>
                    <button type="submit" class="btn btn-teal">Confirmar cierre</button>
                </form>
            @endif
        </div>
        @endif

        {{-- Fechas clave --}}
        <div class="card">
            <h3>Fechas</h3>
            @foreach([
                'Creada'   => $solicitud->sol_fecha_creacion,
                'Enviada'  => $solicitud->sol_fecha_envio,
                'Aprobada' => $solicitud->sol_fecha_aprobacion,
                'Cerrada'  => $solicitud->sol_fecha_cierre,
            ] as $label => $fecha)
                @if($fecha)
                <div class="campo">
                    <div class="etq">{{ $label }}</div>
                    <div class="val" style="font-size:13px;">{{ $fecha->format('d/m/Y H:i') }}</div>
                </div>
                @endif
            @endforeach
        </div>

        {{-- Eliminar (solo borrador) --}}
        @if($solicitud->esBorrador() && ($usuario->esAdmin() || $solicitud->sol_usr_solicita === $usuario->usr_id))
        <div class="card">
            <h3>Zona peligrosa</h3>
            <form method="POST" action="{{ route('solicitudes.destroy', $solicitud) }}"
                  onsubmit="return confirm('¿Eliminar permanentemente esta solicitud?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Eliminar solicitud</button>
            </form>
        </div>
        @endif
    </div>
</div>

@endsection
