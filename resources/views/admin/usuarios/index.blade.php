@extends('layouts.app')

@section('title', 'Usuarios')

@push('styles')
<style>
    .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:12px; }
    .page-header h1 { font-size:20px; color:#1a3a5c; }

    .btn { display:inline-block; padding:7px 16px; border-radius:4px; font-size:13px; text-decoration:none; cursor:pointer; border:none; font-family:inherit; }
    .btn-primary { background:#1a3a5c; color:#fff; }
    .btn-primary:hover { background:#24507e; }
    .btn-sm { padding:4px 10px; font-size:12px; }
    .btn-outline { background:transparent; border:1px solid #ccc; color:#555; }
    .btn-outline:hover { background:#f0f2f5; }
    .btn-danger { background:transparent; border:1px solid #e74c3c; color:#e74c3c; }
    .btn-danger:hover { background:#fdf2f2; }
    .btn-success { background:transparent; border:1px solid #27ae60; color:#27ae60; }
    .btn-success:hover { background:#f0faf5; }

    .filtros { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:16px; align-items:center; }
    .filtros input[type="text"] {
        padding:5px 10px; border:1px solid #d0d5dd; border-radius:4px;
        font-size:13px; font-family:inherit; outline:none; width:220px;
    }
    .filtros input:focus { border-color:#2e6da4; }
    .filtros select {
        padding:5px 10px; border:1px solid #d0d5dd; border-radius:4px;
        font-size:13px; font-family:inherit; outline:none; background:#fff;
    }
    .filtros select:focus { border-color:#2e6da4; }

    .tabla-wrap { background:#fff; border-radius:6px; box-shadow:0 1px 4px rgba(0,0,0,.08); overflow:hidden; }
    table { width:100%; border-collapse:collapse; }
    thead th { background:#f8f9fa; padding:10px 14px; text-align:left; font-size:11px; text-transform:uppercase; letter-spacing:.5px; color:#888; border-bottom:1px solid #e8eaed; }
    tbody td { padding:11px 14px; border-bottom:1px solid #f0f2f5; font-size:13px; }
    tbody tr:last-child td { border-bottom:none; }
    tbody tr:hover td { background:#fafbfc; }

    .badge-rol { display:inline-block; padding:2px 9px; border-radius:20px; font-size:11px; font-weight:600; color:#fff; }
    .badge-activo   { background:#27ae60; color:#fff; }
    .badge-inactivo { background:#aaa; color:#fff; }
    .badge-rol-1 { background:#2e86c1; }
    .badge-rol-2 { background:#8e44ad; }
    .badge-rol-3 { background:#e67e22; }
    .badge-rol-4 { background:#1a3a5c; }

    .acciones { display:flex; gap:6px; }
    .vacío { text-align:center; padding:40px; color:#aaa; font-size:14px; }

    .paginacion { display:flex; justify-content:center; gap:4px; margin-top:20px; }
    .paginacion a, .paginacion span { display:inline-block; padding:5px 10px; border-radius:4px; font-size:13px; text-decoration:none; border:1px solid #ddd; color:#555; background:#fff; }
    .paginacion a:hover { background:#f0f2f5; }
    .paginacion .activa { background:#1a3a5c; color:#fff; border-color:#1a3a5c; }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1>Usuarios</h1>
    <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary">+ Nuevo usuario</a>
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('admin.usuarios.index') }}" class="filtros">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre o email…">
    <select name="rol">
        <option value="">Todos los roles</option>
        @foreach($roles as $rol)
            <option value="{{ $rol->rol_id }}" {{ request('rol') == $rol->rol_id ? 'selected' : '' }}>
                {{ $rol->rol_nombre }}
            </option>
        @endforeach
    </select>
    <select name="estado">
        <option value="">Activos e inactivos</option>
        <option value="activo"   {{ request('estado') === 'activo'   ? 'selected' : '' }}>Solo activos</option>
        <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Solo inactivos</option>
    </select>
    <button type="submit" class="btn btn-outline btn-sm">Filtrar</button>
    @if(request()->hasAny(['q','rol','estado']))
        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline btn-sm">Limpiar</a>
    @endif
</form>

<div class="tabla-wrap">
    @if($usuarios->isEmpty())
        <div class="vacío">No hay usuarios para mostrar.</div>
    @else
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Área</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Creado</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($usuarios as $usr)
            <tr>
                <td style="color:#aaa; font-size:12px;">{{ $usr->usr_id }}</td>
                <td style="font-weight:500;">{{ $usr->usr_nombre }}</td>
                <td style="color:#555;">{{ $usr->usr_email }}</td>
                <td style="font-size:12px; color:#888;">{{ $usr->usr_area ?: '—' }}</td>
                <td>
                    <span class="badge-rol badge-rol-{{ $usr->usr_rol_id }}">
                        {{ $usr->rol->rol_nombre ?? '—' }}
                    </span>
                </td>
                <td>
                    <span class="{{ $usr->usr_activo ? 'badge-activo' : 'badge-inactivo' }}"
                          style="display:inline-block; padding:2px 9px; border-radius:20px; font-size:11px; font-weight:600;">
                        {{ $usr->usr_activo ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
                <td style="font-size:12px; color:#aaa;">
                    {{ $usr->usr_creado ? $usr->usr_creado->format('d/m/Y') : '—' }}
                </td>
                <td>
                    <div class="acciones">
                        <a href="{{ route('admin.usuarios.edit', $usr) }}" class="btn btn-sm btn-outline">Editar</a>
                        <form method="POST" action="{{ route('admin.usuarios.toggle', $usr) }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $usr->usr_activo ? 'btn-danger' : 'btn-success' }}">
                                {{ $usr->usr_activo ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

@if($usuarios->hasPages())
<div class="paginacion">
    @if($usuarios->onFirstPage()) <span>«</span> @else <a href="{{ $usuarios->previousPageUrl() }}">«</a> @endif
    @foreach($usuarios->getUrlRange(1, $usuarios->lastPage()) as $page => $url)
        @if($page == $usuarios->currentPage()) <span class="activa">{{ $page }}</span>
        @else <a href="{{ $url }}">{{ $page }}</a>
        @endif
    @endforeach
    @if($usuarios->hasMorePages()) <a href="{{ $usuarios->nextPageUrl() }}">»</a> @else <span>»</span> @endif
</div>
@endif

@endsection
