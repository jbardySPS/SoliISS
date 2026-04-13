@extends('layouts.app')

@section('title', 'Editar solicitud')

@push('styles')
<style>
    .form-card {
        background: #fff;
        border-radius: 6px;
        box-shadow: 0 1px 4px rgba(0,0,0,.08);
        padding: 28px 32px;
        max-width: 680px;
    }
    .form-card h2 { font-size: 18px; color: #1a3a5c; margin-bottom: 4px; }
    .form-card .subtitulo { font-size: 12px; color: #888; margin-bottom: 24px; }

    .form-group { margin-bottom: 18px; }
    .form-group label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #555;
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: .4px;
    }
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid #d0d5dd;
        border-radius: 4px;
        font-size: 14px;
        font-family: inherit;
        color: #333;
        background: #fff;
        outline: none;
    }
    .form-group select:focus,
    .form-group textarea:focus { border-color: #2e6da4; }
    .form-group textarea { resize: vertical; min-height: 120px; }
    .form-group .error { font-size: 12px; color: #c0392b; margin-top: 4px; }

    .radio-group { display: flex; gap: 16px; }
    .radio-group label {
        display: flex; align-items: center; gap: 6px;
        font-size: 13px; font-weight: 400;
        text-transform: none; letter-spacing: 0;
        color: #333; cursor: pointer;
    }
    .radio-group input[type="radio"] { accent-color: #1a3a5c; }

    .form-actions {
        display: flex; gap: 10px; align-items: center;
        margin-top: 24px; padding-top: 20px;
        border-top: 1px solid #f0f2f5;
    }
    .btn {
        padding: 8px 20px; border-radius: 4px; font-size: 13px;
        font-family: inherit; cursor: pointer; border: none;
        text-decoration: none; display: inline-block;
    }
    .btn-primary { background: #1a3a5c; color: #fff; }
    .btn-primary:hover { background: #24507e; }
    .btn-outline { background: transparent; border: 1px solid #ccc; color: #555; }
    .btn-outline:hover { background: #f0f2f5; }

    .char-count { font-size: 11px; color: #aaa; text-align: right; margin-top: 3px; }
</style>
@endpush

@section('content')

<div style="margin-bottom: 16px;">
    <a href="{{ route('solicitudes.show', $solicitud) }}"
       style="font-size:13px; color:#2e6da4; text-decoration:none;">← Volver</a>
</div>

<div class="form-card">
    <h2>Editar solicitud</h2>
    <p class="subtitulo">{{ $solicitud->numero }}</p>

    <form method="POST" action="{{ route('solicitudes.update', $solicitud) }}">
        @csrf
        @method('PUT')

        {{-- Tipo --}}
        <div class="form-group">
            <label for="sol_tipo">Tipo de solicitud</label>
            <select name="sol_tipo" id="sol_tipo">
                <option value="">— Seleccioná —</option>
                @foreach(\App\Models\Solicitud::TIPOS as $key => $label)
                    <option value="{{ $key }}"
                        {{ old('sol_tipo', $solicitud->sol_tipo) === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('sol_tipo')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Prioridad --}}
        <div class="form-group">
            <label>Prioridad</label>
            <div class="radio-group">
                @foreach(['baja' => 'Baja', 'media' => 'Media', 'alta' => 'Alta'] as $val => $lbl)
                    <label>
                        <input type="radio" name="sol_prioridad" value="{{ $val }}"
                            {{ old('sol_prioridad', $solicitud->sol_prioridad) === $val ? 'checked' : '' }}>
                        {{ $lbl }}
                    </label>
                @endforeach
            </div>
            @error('sol_prioridad')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Descripción --}}
        <div class="form-group">
            <label for="sol_descripcion">Descripción</label>
            <textarea name="sol_descripcion" id="sol_descripcion"
                      maxlength="2000">{{ old('sol_descripcion', $solicitud->sol_descripcion) }}</textarea>
            <div class="char-count"><span id="charCount">0</span> / 2000</div>
            @error('sol_descripcion')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
            <a href="{{ route('solicitudes.show', $solicitud) }}" class="btn btn-outline">Cancelar</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const textarea = document.getElementById('sol_descripcion');
    const counter  = document.getElementById('charCount');
    const update   = () => counter.textContent = textarea.value.length;
    textarea.addEventListener('input', update);
    update();
</script>
@endpush

@endsection
