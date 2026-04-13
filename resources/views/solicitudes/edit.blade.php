@extends('layouts.app')

@section('title', 'Editar solicitud')

@push('styles')
<style>
    .form-card { background:#fff; border-radius:6px; box-shadow:0 1px 4px rgba(0,0,0,.08); padding:28px 32px; max-width:700px; }
    .form-card h2 { font-size:18px; color:#1a3a5c; margin-bottom:4px; }
    .form-card .sub { font-size:12px; color:#888; margin-bottom:24px; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    @media(max-width:600px){ .form-row { grid-template-columns:1fr; } }
    .form-group { margin-bottom:18px; }
    .form-group label { display:block; font-size:12px; font-weight:600; color:#555; margin-bottom:5px; text-transform:uppercase; letter-spacing:.4px; }
    .form-group select, .form-group input[type="text"], .form-group textarea {
        width:100%; padding:8px 10px; border:1px solid #d0d5dd; border-radius:4px;
        font-size:14px; font-family:inherit; color:#333; background:#fff; outline:none;
    }
    .form-group select:focus, .form-group input:focus, .form-group textarea:focus { border-color:#2e6da4; }
    .form-group textarea { resize:vertical; min-height:120px; }
    .form-group .error { font-size:12px; color:#c0392b; margin-top:4px; }
    .radio-group { display:flex; gap:20px; }
    .radio-group label { display:flex; align-items:center; gap:6px; font-size:13px; font-weight:400; text-transform:none; letter-spacing:0; color:#333; cursor:pointer; }
    .form-actions { display:flex; gap:10px; align-items:center; margin-top:24px; padding-top:20px; border-top:1px solid #f0f2f5; }
    .btn { padding:8px 20px; border-radius:4px; font-size:13px; font-family:inherit; cursor:pointer; border:none; text-decoration:none; display:inline-block; }
    .btn-primary { background:#1a3a5c; color:#fff; }
    .btn-primary:hover { background:#24507e; }
    .btn-outline { background:transparent; border:1px solid #ccc; color:#555; }
    .btn-outline:hover { background:#f0f2f5; }
    .char-count { font-size:11px; color:#aaa; text-align:right; margin-top:3px; }
</style>
@endpush

@section('content')
<div style="margin-bottom:16px;">
    <a href="{{ route('solicitudes.show', $solicitud) }}" style="font-size:13px; color:#2e6da4; text-decoration:none;">← Volver</a>
</div>

<div class="form-card">
    <h2>Editar solicitud</h2>
    <p class="sub">{{ $solicitud->sol_numero }}</p>

    <form method="POST" action="{{ route('solicitudes.update', $solicitud) }}">
        @csrf
        @method('PUT')

        <div class="form-row">
            <div class="form-group">
                <label>Tipo de solicitud</label>
                <select name="sol_tipo_id">
                    @foreach($tipos as $tipo)
                        <option value="{{ $tipo->tipo_id }}" {{ old('sol_tipo_id', $solicitud->sol_tipo_id) == $tipo->tipo_id ? 'selected' : '' }}>
                            {{ $tipo->tipo_nombre }}
                        </option>
                    @endforeach
                </select>
                @error('sol_tipo_id') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label>Área destino</label>
                <select name="sol_area_dest_id">
                    @foreach($areas as $area)
                        <option value="{{ $area->area_id }}" {{ old('sol_area_dest_id', $solicitud->sol_area_dest_id) == $area->area_id ? 'selected' : '' }}>
                            {{ $area->area_nombre }}
                        </option>
                    @endforeach
                </select>
                @error('sol_area_dest_id') <div class="error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-group">
            <label>Título</label>
            <input type="text" name="sol_titulo" maxlength="200"
                   value="{{ old('sol_titulo', $solicitud->sol_titulo) }}">
            @error('sol_titulo') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label>Descripción</label>
            <textarea name="sol_descripcion" id="sol_descripcion" maxlength="4000">{{ old('sol_descripcion', $solicitud->sol_descripcion) }}</textarea>
            <div class="char-count"><span id="charCount">0</span> / 4000</div>
            @error('sol_descripcion') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Prioridad</label>
                <div class="radio-group">
                    @foreach([1 => ['Baja','#27ae60'], 2 => ['Media','#e67e22'], 3 => ['Alta','#c0392b']] as $val => $info)
                        <label>
                            <input type="radio" name="sol_prioridad" value="{{ $val }}"
                                {{ old('sol_prioridad', $solicitud->sol_prioridad) == $val ? 'checked' : '' }}>
                            <span style="color:{{ $info[1] }}; font-weight:600;">{{ $info[0] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <label>Supervisor</label>
                <select name="sol_usr_supervisor">
                    @foreach($supervisores as $sup)
                        <option value="{{ $sup->usr_id }}" {{ old('sol_usr_supervisor', $solicitud->sol_usr_supervisor) == $sup->usr_id ? 'selected' : '' }}>
                            {{ $sup->usr_nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Sistema / Aplicación <span style="color:#aaa; font-weight:400;">(opcional)</span></label>
            <input type="text" name="sol_sistema" maxlength="100"
                   value="{{ old('sol_sistema', $solicitud->sol_sistema) }}">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
            <a href="{{ route('solicitudes.show', $solicitud) }}" class="btn btn-outline">Cancelar</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const ta = document.getElementById('sol_descripcion');
    const ct = document.getElementById('charCount');
    const upd = () => ct.textContent = ta.value.length;
    ta.addEventListener('input', upd); upd();
</script>
@endpush
@endsection
