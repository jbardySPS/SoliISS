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
    $saludo = $saludos[$usuario->USR_ROL_ID] ?? '';
@endphp

<div style="margin-bottom: 24px;">
    <h1 style="font-size: 20px; color: #1a3a5c; margin-bottom: 4px;">
        Bienvenido, {{ $usuario->USR_NOMBRE }}
    </h1>
    <p style="color: #666; font-size: 13px;">{{ $saludo }}</p>
</div>

{{-- Tarjetas de acceso rápido --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">

    @if($usuario->tieneRol([1, 2, 3, 4]))
    <div style="background:#fff; border-radius:6px; padding:20px; box-shadow:0 1px 4px rgba(0,0,0,.08);">
        <div style="font-size:13px; color:#888; margin-bottom:4px;">MIS SOLICITUDES</div>
        <div style="font-size:28px; font-weight:700; color:#1a3a5c;">—</div>
        <div style="font-size:12px; color:#aaa; margin-top:4px;">Disponible en breve</div>
    </div>
    @endif

    @if($usuario->tieneRol([2, 3, 4]))
    <div style="background:#fff; border-radius:6px; padding:20px; box-shadow:0 1px 4px rgba(0,0,0,.08);">
        <div style="font-size:13px; color:#888; margin-bottom:4px;">PENDIENTES</div>
        <div style="font-size:28px; font-weight:700; color:#e67e22;">—</div>
        <div style="font-size:12px; color:#aaa; margin-top:4px;">Disponible en breve</div>
    </div>
    @endif

    @if($usuario->esAdmin())
    <div style="background:#fff; border-radius:6px; padding:20px; box-shadow:0 1px 4px rgba(0,0,0,.08);">
        <div style="font-size:13px; color:#888; margin-bottom:4px;">USUARIOS</div>
        <div style="font-size:28px; font-weight:700; color:#2e6da4;">—</div>
        <div style="font-size:12px; color:#aaa; margin-top:4px;">ABM disponible en breve</div>
    </div>
    @endif

</div>

@endsection
