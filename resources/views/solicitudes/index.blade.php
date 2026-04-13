@extends('layouts.app')

@section('title', 'Solicitudes')

@push('styles')
<style>
    .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:12px; }
    .page-header h1 { font-size:20px; color:#1a3a5c; }

    .btn { display:inline-block; padding:7px 16px; border-radius:4px; font-size:13px; text-decoration:none; cursor:pointer; border:none; font-family:inherit; }
    .btn-primary { background:#1a3a5c; color:#fff; }
    .btn-primary:hover { background:#24507e; }

    .filtros { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:16px; }
    .filtro-btn { padding:3px 12px; border-radius:20px; font-size:12px; text-decoration:none; border:1px solid #ddd; color:#555; background:#fff; }
    .filtro-btn.activo { background:#1a3a5c; color:#fff; border-color:#1a3a5c; }
    .filtro-btn:hover:not(.activo) { background:#f0f2f5; }

    .tabla-wrap { background:#fff; border-radius:6px; box-shadow:0 1px 4px rgba(0,0,0,.08); overflow:hidden; }
    table { width:100%; border-collapse:collapse; }
    thead th { background:#f8f9fa; padding:10px 14px; text-align:left; font-size:11px; text-transform:uppercase; letter-spacing:.5px; color:#888; border-bottom:1px solid #e8eaed; }
    tbody td { padding:12px 14px; border-bottom:1px solid #f0f2f5; font-size:13px; }
    tbody tr:last-child td { border-bottom:none; }
    tbody tr:hover td { background:#fafbfc; }

    .badge { display:inline-block; padding:2px 10px; border-radius:20px; font-size:11px; font-weight:600; color:#fff; }
    .badge-prio { display:inline-block; padding:2px 8px; border-radius:4px; font-size:11px; font-weight:600; }
    .link-ver { color:#2e6da4; text-decoration:none; font-weight:500; }
    .link-ver:hover { text-decoration:underline; }
    .vacío { text-align:center; padding:40px; color:#aaa; font-size:14px; }

    .paginacion { display:flex; justify-content:center; gap:4px; margin-top:20px; }
    .paginacion a, .paginacion span { display:inline-block; padding:5px 10px; border-radius:4px; font-size:13px; text-decoration:none; border:1px solid #ddd; color:#555; background:#fff; }
    .paginacion a:hover { background:#f0f2f5; }
    .paginacion .activa { background:#1a3a5c; color:#fff; border-color:#1a3a5c; }
</style>
@endpush

@section('content')
@php $usuario = Auth::user(); @endphp

<div class="page-header">
    <h1>Solicitudes</h1>
    @if($usuario->tieneRol([1, 4]))
        <a href="{{ route('solicitudes.create') }}" class="btn btn-primary">+ Nueva solicitud</a>
    @endif
</div>

{{-- Filtros por estado --}}
<div class="filtros">
    <a href="{{ route('solicitudes.index') }}"
       class="filtro-btn {{ !$estadoFiltro ? 'activo' : '' }}">Todas</a>
    @foreach($estados as $est)
        <a href="{{ route('solicitudes.index', ['estado' => $est->est_id]) }}"
           class="filtro-btn {{ $estadoFiltro == $est->est_id ? 'activo' : '' }}"
           style="{{ $estadoFiltro == $est->est_id ? 'background:'.$est->est_color.'; border-color:'.$est->est_color.';' : '' }}">
            {{ $est->est_nombre }}
        </a>
    @endforeach
</div>

<div class="tabla-wrap">
    @if($solicitudes->isEmpty())
        <div class="vacío">No hay solicitudes para mostrar.</div>
    @else
        <table>
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Tipo</th>
                    <th>Título</th>
                    @if(!$usuario->esSolicitante()) <th>Solicitante</th> @endif
                    <th>Área</th>
                    <th>Prio.</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($solicitudes as $sol)
                <tr>
                    <td style="color:#888; font-size:12px; white-space:nowrap;">{{ $sol->sol_numero }}</td>
                    <td style="font-size:12px;">{{ $sol->tipo->tipo_nombre ?? '—' }}</td>
                    <td style="max-width:260px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-weight:500;">
                        {{ $sol->sol_titulo }}
                    </td>
                    @if(!$usuario->esSolicitante())
                        <td>{{ $sol->solicitante->usr_nombre ?? '—' }}</td>
                    @endif
                    <td style="font-size:12px;">{{ $sol->area->area_nombre ?? '—' }}</td>
                    <td>
                        <span class="badge-prio" style="color:{{ $sol->prioridad_color }}; background:{{ $sol->prioridad_color }}22;">
                            {{ $sol->prioridad_label }}
                        </span>
                    </td>
                    <td>
                        @if($sol->estado)
                            <span class="badge" style="background:{{ $sol->estado->est_color ?? '#999' }};">
                                {{ $sol->estado->est_nombre }}
                            </span>
                        @endif
                    </td>
                    <td style="color:#888; font-size:12px; white-space:nowrap;">
                        {{ $sol->sol_fecha_creacion ? $sol->sol_fecha_creacion->format('d/m/Y') : '—' }}
                    </td>
                    <td>
                        <a href="{{ route('solicitudes.show', $sol) }}" class="link-ver">Ver</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

@if($solicitudes->hasPages())
<div class="paginacion">
    @if($solicitudes->onFirstPage()) <span>«</span> @else <a href="{{ $solicitudes->previousPageUrl() }}">«</a> @endif
    @foreach($solicitudes->getUrlRange(1, $solicitudes->lastPage()) as $page => $url)
        @if($page == $solicitudes->currentPage()) <span class="activa">{{ $page }}</span>
        @else <a href="{{ $url }}">{{ $page }}</a>
        @endif
    @endforeach
    @if($solicitudes->hasMorePages()) <a href="{{ $solicitudes->nextPageUrl() }}">»</a> @else <span>»</span> @endif
</div>
@endif

@endsection
