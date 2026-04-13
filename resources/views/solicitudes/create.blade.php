@extends('layouts.app')

@section('title', 'Nueva solicitud')

@push('styles')
<style>
    .form-card { background:#fff; border-radius:6px; box-shadow:0 1px 4px rgba(0,0,0,.08); padding:28px 32px; max-width:700px; }
    .form-card h2 { font-size:18px; color:#1a3a5c; margin-bottom:24px; }

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
    .radio-group input[type="radio"] { accent-color:#1a3a5c; }

    .form-actions { display:flex; gap:10px; align-items:center; margin-top:24px; padding-top:20px; border-top:1px solid #f0f2f5; }
    .btn { padding:8px 20px; border-radius:4px; font-size:13px; font-family:inherit; cursor:pointer; border:none; text-decoration:none; display:inline-block; }
    .btn-primary { background:#1a3a5c; color:#fff; }
    .btn-primary:hover { background:#24507e; }
    .btn-success { background:#27ae60; color:#fff; }
    .btn-success:hover { background:#219a52; }
    .btn-outline { background:transparent; border:1px solid #ccc; color:#555; }
    .btn-outline:hover { background:#f0f2f5; }

    .char-count { font-size:11px; color:#aaa; text-align:right; margin-top:3px; }
</style>
@endpush

@section('content')
<div style="margin-bottom:16px;">
    <a href="{{ route('solicitudes.index') }}" style="font-size:13px; color:#2e6da4; text-decoration:none;">← Volver</a>
</div>

<div class="form-card">
    <h2>Nueva solicitud</h2>

    <form method="POST" action="{{ route('solicitudes.store') }}">
        @csrf

        <div class="form-row">
            {{-- Tipo --}}
            <div class="form-group">
                <label>Tipo de solicitud</label>
                <select name="sol_tipo_id">
                    <option value="">— Seleccioná —</option>
                    @foreach($tipos as $tipo)
                        <option value="{{ $tipo->tipo_id }}" {{ old('sol_tipo_id') == $tipo->tipo_id ? 'selected' : '' }}>
                            {{ $tipo->tipo_nombre }}
                        </option>
                    @endforeach
                </select>
                @error('sol_tipo_id') <div class="error">{{ $message }}</div> @enderror
            </div>

            {{-- Área destino --}}
            <div class="form-group">
                <label>Área destino</label>
                <select name="sol_area_dest_id">
                    <option value="">— Seleccioná —</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->area_id }}" {{ old('sol_area_dest_id') == $area->area_id ? 'selected' : '' }}>
                            {{ $area->area_nombre }}
                        </option>
                    @endforeach
                </select>
                @error('sol_area_dest_id') <div class="error">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- Título --}}
        <div class="form-group">
            <label>Título</label>
            <input type="text" name="sol_titulo" maxlength="200"
                   value="{{ old('sol_titulo') }}" placeholder="Resumen breve de la solicitud">
            @error('sol_titulo') <div class="error">{{ $message }}</div> @enderror
        </div>

        {{-- Descripción --}}
        <div class="form-group">
            <label>Descripción</label>
            <textarea name="sol_descripcion" id="sol_descripcion" maxlength="4000"
                      placeholder="Describí la solicitud con el mayor detalle posible...">{{ old('sol_descripcion') }}</textarea>
            <div class="char-count"><span id="charCount">0</span> / 4000</div>
            @error('sol_descripcion') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="form-row">
            {{-- Prioridad --}}
            <div class="form-group">
                <label>Prioridad</label>
                <div class="radio-group">
                    @foreach([1 => ['Baja','#27ae60'], 2 => ['Media','#e67e22'], 3 => ['Alta','#c0392b']] as $val => $info)
                        <label>
                            <input type="radio" name="sol_prioridad" value="{{ $val }}"
                                {{ old('sol_prioridad', 2) == $val ? 'checked' : '' }}>
                            <span style="color:{{ $info[1] }}; font-weight:600;">{{ $info[0] }}</span>
                        </label>
                    @endforeach
                </div>
                @error('sol_prioridad') <div class="error">{{ $message }}</div> @enderror
            </div>

            {{-- Supervisor --}}
            <div class="form-group">
                <label>Supervisor asignado</label>
                <select name="sol_usr_supervisor">
                    <option value="">— Seleccioná —</option>
                    @foreach($supervisores as $sup)
                        <option value="{{ $sup->usr_id }}" {{ old('sol_usr_supervisor') == $sup->usr_id ? 'selected' : '' }}>
                            {{ $sup->usr_nombre }}
                        </option>
                    @endforeach
                </select>
                @error('sol_usr_supervisor') <div class="error">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- Sistema (opcional) --}}
        <div class="form-group">
            <label>Sistema / Aplicación <span style="color:#aaa; font-weight:400;">(opcional)</span></label>
            <input type="text" name="sol_sistema" maxlength="100"
                   value="{{ old('sol_sistema') }}" placeholder="Ej: SOLI-ISS, SIG, etc.">
        </div>

        <div class="form-actions">
            <button type="submit" name="enviar" value="1" class="btn btn-success">Enviar solicitud</button>
            <button type="submit" class="btn btn-outline">Guardar borrador</button>
            <a href="{{ route('solicitudes.index') }}" class="btn btn-outline">Cancelar</a>
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
