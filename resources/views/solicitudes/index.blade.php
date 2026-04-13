@extends('layouts.app')

@section('title', 'Solicitudes')

@push('styles')
<style>
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 12px;
    }
    .page-header h1 { font-size: 20px; color: #1a3a5c; }

    .btn {
        display: inline-block;
        padding: 7px 16px;
        border-radius: 4px;
        font-size: 13px;
        text-decoration: none;
        cursor: pointer;
        border: none;
        font-family: inherit;
    }
    .btn-primary { background: #1a3a5c; color: #fff; }
    .btn-primary:hover { background: #24507e; }

    /* Filtros de estado */
    .filtros {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }
    .filtro-btn {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        text-decoration: none;
        border: 1px solid #ddd;
        color: #555;
        background: #fff;
    }
    .filtro-btn.activo {
        background: #1a3a5c;
        color: #fff;
        border-color: #1a3a5c;
    }
    .filtro-btn:hover:not(.activo) { background: #f0f2f5; }

    /* Tabla */
    .tabla-wrap {
        background: #fff;
        border-radius: 6px;
        box-shadow: 0 1px 4px rgba(0,0,0,.08);
        overflow: hidden;
    }
    table { width: 100%; border-collapse: collapse; }
    thead th {
        background: #f8f9fa;
        padding: 10px 14px;
        text-align: left;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #888;
        border-bottom: 1px solid #e8eaed;
    }
    tbody td {
        padding: 12px 14px;
        border-bottom: 1px solid #f0f2f5;
        font-size: 13px;
    }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover td { background: #fafbfc; }

    .badge-estado {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        color: #fff;
    }
    .badge-prioridad {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
    }
    .link-ver { color: #2e6da4; text-decoration: none; font-weight: 500; }
    .link-ver:hover { text-decoration: underline; }

    .vacío {
        text-align: center;
        padding: 40px;
        color: #aaa;
        font-size: 14px;
    }

    /* Paginación */
    .paginacion {
        display: flex;
        justify-content: center;
        gap: 4px;
        margin-top: 20px;
    }
    .paginacion a, .paginacion span {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 13px;
        text-decoration: none;
        border: 1px solid #ddd;
        color: #555;
        background: #fff;
    }
    .paginacion a:hover { background: #f0f2f5; }
    .paginacion .activa { background: #1a3a5c; color: #fff; border-color: #1a3a5c; }
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
@php
    $estados = [
        ''           => 'Todas',
        'borrador'   => 'Borrador',
        'pendiente'  => 'Pendiente',
        'aprobada'   => 'Aprobada',
        'en_proceso' => 'En proceso',
        'completada' => 'Completada',
        'rechazada'  => 'Rechazada',
        'cancelada'  => 'Cancelada',
    ];
@endphp
<div class="filtros">
    @foreach($estados as $val => $label)
        <a href="{{ route('solicitudes.index', $val ? ['estado' => $val] : []) }}"
           class="filtro-btn {{ $estadoFiltro === $val || ($val === '' && !$estadoFiltro) ? 'activo' : '' }}">
            {{ $label }}
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
                    <th>Descripción</th>
                    @if(!$usuario->esSolicitante())
                        <th>Solicitante</th>
                    @endif
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($solicitudes as $sol)
                <tr>
                    <td style="color:#888; font-size:12px;">{{ $sol->numero }}</td>
                    <td>{{ $sol->tipo_label }}</td>
                    <td style="max-width:280px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                        {{ $sol->sol_descripcion }}
                    </td>
                    @if(!$usuario->esSolicitante())
                        <td>{{ $sol->solicitante->usr_nombre ?? '—' }}</td>
                    @endif
                    <td>
                        <span class="badge-prioridad"
                              style="color:{{ $sol->prioridad_color }}; background:{{ $sol->prioridad_color }}22;">
                            {{ $sol->prioridad_label }}
                        </span>
                    </td>
                    <td>
                        <span class="badge-estado" style="background:{{ $sol->estado_color }};">
                            {{ $sol->estado_label }}
                        </span>
                    </td>
                    <td style="color:#888; font-size:12px; white-space:nowrap;">
                        {{ $sol->sol_creado ? $sol->sol_creado->format('d/m/Y') : '—' }}
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

{{-- Paginación --}}
@if($solicitudes->hasPages())
<div class="paginacion">
    @if($solicitudes->onFirstPage())
        <span>«</span>
    @else
        <a href="{{ $solicitudes->previousPageUrl() }}">«</a>
    @endif

    @foreach($solicitudes->getUrlRange(1, $solicitudes->lastPage()) as $page => $url)
        @if($page == $solicitudes->currentPage())
            <span class="activa">{{ $page }}</span>
        @else
            <a href="{{ $url }}">{{ $page }}</a>
        @endif
    @endforeach

    @if($solicitudes->hasMorePages())
        <a href="{{ $solicitudes->nextPageUrl() }}">»</a>
    @else
        <span>»</span>
    @endif
</div>
@endif

@endsection
