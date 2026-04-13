@extends('layouts.app')

@section('title', 'Editar usuario')

@push('styles')
<style>
    .form-card { background:#fff; border-radius:6px; box-shadow:0 1px 4px rgba(0,0,0,.08); padding:28px 32px; max-width:600px; }
    .form-card h2 { font-size:18px; color:#1a3a5c; margin-bottom:4px; }
    .form-card .sub { font-size:12px; color:#888; margin-bottom:24px; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    @media(max-width:600px){ .form-row { grid-template-columns:1fr; } }
    .form-group { margin-bottom:18px; }
    .form-group label { display:block; font-size:12px; font-weight:600; color:#555; margin-bottom:5px; text-transform:uppercase; letter-spacing:.4px; }
    .form-group input, .form-group select {
        width:100%; padding:8px 10px; border:1px solid #d0d5dd; border-radius:4px;
        font-size:14px; font-family:inherit; color:#333; background:#fff; outline:none;
    }
    .form-group input:focus, .form-group select:focus { border-color:#2e6da4; }
    .form-group .error { font-size:12px; color:#c0392b; margin-top:4px; }
    .form-group .hint  { font-size:11px; color:#aaa; margin-top:3px; }
    .toggle-wrap { display:flex; align-items:center; gap:10px; }
    .toggle-wrap input[type="checkbox"] { width:16px; height:16px; accent-color:#1a3a5c; cursor:pointer; }
    .toggle-wrap label { font-size:13px; font-weight:400; text-transform:none; letter-spacing:0; color:#333; cursor:pointer; }
    .form-actions { display:flex; gap:10px; align-items:center; margin-top:24px; padding-top:20px; border-top:1px solid #f0f2f5; }
    .btn { padding:8px 20px; border-radius:4px; font-size:13px; font-family:inherit; cursor:pointer; border:none; text-decoration:none; display:inline-block; }
    .btn-primary { background:#1a3a5c; color:#fff; }
    .btn-primary:hover { background:#24507e; }
    .btn-outline { background:transparent; border:1px solid #ccc; color:#555; }
    .btn-outline:hover { background:#f0f2f5; }
</style>
@endpush

@section('content')
<div style="margin-bottom:16px;">
    <a href="{{ route('admin.usuarios.index') }}" style="font-size:13px; color:#2e6da4; text-decoration:none;">← Volver</a>
</div>

<div class="form-card">
    <h2>Editar usuario</h2>
    <p class="sub">#{{ $usuario->usr_id }} · {{ $usuario->usr_email }}</p>

    <form method="POST" action="{{ route('admin.usuarios.update', $usuario) }}">
        @csrf
        @method('PUT')

        <div class="form-row">
            <div class="form-group">
                <label>Nombre completo</label>
                <input type="text" name="usr_nombre" maxlength="100"
                       value="{{ old('usr_nombre', $usuario->usr_nombre) }}">
                @error('usr_nombre') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="usr_email" maxlength="100"
                       value="{{ old('usr_email', $usuario->usr_email) }}">
                @error('usr_email') <div class="error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Nueva contraseña <span style="color:#aaa; font-weight:400;">(opcional)</span></label>
                <input type="password" name="usr_password" autocomplete="new-password"
                       placeholder="Dejá vacío para no cambiar">
                <div class="hint">Mínimo 8 caracteres si cambiás.</div>
                @error('usr_password') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label>Rol</label>
                <select name="usr_rol_id">
                    @foreach($roles as $rol)
                        <option value="{{ $rol->rol_id }}"
                            {{ old('usr_rol_id', $usuario->usr_rol_id) == $rol->rol_id ? 'selected' : '' }}>
                            {{ $rol->rol_nombre }}
                        </option>
                    @endforeach
                </select>
                @error('usr_rol_id') <div class="error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-group">
            <label>Área / Sector <span style="color:#aaa; font-weight:400;">(opcional)</span></label>
            <input type="text" name="usr_area" maxlength="100"
                   value="{{ old('usr_area', $usuario->usr_area) }}"
                   placeholder="Ej: Informática, Contabilidad…">
        </div>

        <div class="form-group">
            <div class="toggle-wrap">
                <input type="checkbox" name="usr_activo" id="usr_activo" value="1"
                       {{ old('usr_activo', $usuario->usr_activo) ? 'checked' : '' }}>
                <label for="usr_activo">Usuario activo (puede ingresar al sistema)</label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline">Cancelar</a>
        </div>
    </form>
</div>
@endsection
