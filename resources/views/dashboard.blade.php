@extends('layouts.app')

@section('title', 'Inicio')

@section('content')

@php
    $usuario = Auth::user();
    $saludos = [
        1 => 'Desde acá podés crear y hacer seguimiento de tus solicitudes.',
        2 => 'Tenés solicitudes de tu equipo pendientes de revisión.',
        3 => 'Revisá la cola de trabajo asignada a tu área.',
        4 => 'Tenés acceso completo al sistema.',
    ];
    $saludo = $saludos[$usuario->usr_rol_id] ?? '';
@endphp

<div style="margin-bottom: 24px;">
    <h1 style="font-size: 20px; color: #1a3a5c; margin-bottom: 4px;">
        Bienvenido, {{ $usuario->usr_nombre }}
    </h1>
    <p style="color: #666; font-size: 13px;">{{ $saludo }}</p>
</div>

{{-- Tarjetas de acceso rápido --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">

    @if($usuario->tieneRol([1, 2, 3, 4]))
    <a href="{{ route('solicitudes.index') }}"
       style="text-decoration:none; display:block; background:#fff; border-radius:6px; padding:20px;
              box-shadow:0 1px 4px rgba(0,0,0,.08); transition: box-shadow .15s;"
       onmouseover="this.style.boxShadow='0 2px 8px rgba(0,0,0,.14)'"
       onmouseout="this.style.boxShadow='0 1px 4px rgba(0,0,0,.08)'">
        <div style="font-size:13px; color:#888; margin-bottom:4px;">
            @if($usuario->esSolicitante()) MIS SOLICITUDES
            @elseif($usuario->esSupervisor()) TODAS LAS SOLICITUDES
            @elseif($usuario->esOperador()) COLA DE TRABAJO
            @else SOLICITUDES
            @endif
        </div>
        <div style="font-size:28px; font-weight:700; color:#1a3a5c;">
            {{ $conteoMisSolicitudes ?? '—' }}
        </div>
        <div style="font-size:12px; color:#2e6da4; margin-top:4px;">Ver solicitudes →</div>
    </a>
    @endif

    @if($usuario->tieneRol([2, 3, 4]))
    <a href="{{ route('solicitudes.index', ['estado' => $usuario->esSupervisor() ? 'pendiente' : 'aprobada']) }}"
       style="text-decoration:none; display:block; background:#fff; border-radius:6px; padding:20px;
              box-shadow:0 1px 4px rgba(0,0,0,.08); transition: box-shadow .15s;"
       onmouseover="this.style.boxShadow='0 2px 8px rgba(0,0,0,.14)'"
       onmouseout="this.style.boxShadow='0 1px 4px rgba(0,0,0,.08)'">
        <div style="font-size:13px; color:#888; margin-bottom:4px;">PENDIENTES</div>
        <div style="font-size:28px; font-weight:700; color:#e67e22;">
            {{ $conteoPendientes ?? '—' }}
        </div>
        <div style="font-size:12px; color:#e67e22; margin-top:4px;">
            @if($usuario->esSupervisor()) Para revisar →
            @else Para procesar →
            @endif
        </div>
    </a>
    @endif

    @if($usuario->esAdmin())
    <a href="{{ route('solicitudes.index') }}"
       style="text-decoration:none; display:block; background:#fff; border-radius:6px; padding:20px;
              box-shadow:0 1px 4px rgba(0,0,0,.08); transition: box-shadow .15s;"
       onmouseover="this.style.boxShadow='0 2px 8px rgba(0,0,0,.14)'"
       onmouseout="this.style.boxShadow='0 1px 4px rgba(0,0,0,.08)'">
        <div style="font-size:13px; color:#888; margin-bottom:4px;">TOTAL EN SISTEMA</div>
        <div style="font-size:28px; font-weight:700; color:#2e6da4;">
            {{ $conteoTotal ?? '—' }}
        </div>
        <div style="font-size:12px; color:#2e6da4; margin-top:4px;">Ver todas →</div>
    </a>
    @endif

</div>

@endsection
