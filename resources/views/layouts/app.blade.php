<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SOLI-ISS') — ISS La Pampa</title>
    <style>
        /* ── Reset y base ───────────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            font-size: 14px;
            background: #f0f2f5;
            color: #333;
            min-height: 100vh;
        }

        /* ── Barra de navegación ────────────────────────────────────────── */
        .navbar {
            background: #1a3a5c;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            height: 52px;
            box-shadow: 0 2px 4px rgba(0,0,0,.25);
        }
        .navbar-brand {
            font-size: 16px;
            font-weight: 600;
            letter-spacing: .5px;
        }
        .navbar-brand span { color: #7eb3e8; }
        .navbar-user {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 13px;
        }
        .navbar-user .badge {
            background: #2e6da4;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
        }
        .navbar-user form button {
            background: transparent;
            border: 1px solid rgba(255,255,255,.35);
            color: #fff;
            padding: 4px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        .navbar-user form button:hover { background: rgba(255,255,255,.1); }

        /* ── Contenedor principal ───────────────────────────────────────── */
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 24px 16px;
        }

        /* ── Alertas ────────────────────────────────────────────────────── */
        .alert {
            padding: 10px 16px;
            border-radius: 4px;
            margin-bottom: 16px;
            font-size: 13px;
        }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error   { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-info    { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
    </style>
    @stack('styles')
</head>
<body>

{{-- Barra de navegación --}}
<nav class="navbar">
    <div style="display:flex; align-items:center; gap:24px;">
        <a href="{{ route('dashboard') }}" class="navbar-brand" style="text-decoration:none; color:#fff;">
            SOLI-<span>ISS</span>
        </a>
        @auth
        <a href="{{ route('solicitudes.index') }}"
           style="color:rgba(255,255,255,.8); text-decoration:none; font-size:13px;
                  {{ request()->routeIs('solicitudes.*') ? 'color:#fff; font-weight:600; border-bottom:2px solid #7eb3e8;' : '' }}">
            Solicitudes
        </a>
        @if(Auth::user()->esAdmin())
        <a href="{{ route('admin.usuarios.index') }}"
           style="color:rgba(255,255,255,.8); text-decoration:none; font-size:13px;
                  {{ request()->routeIs('admin.usuarios.*') ? 'color:#fff; font-weight:600; border-bottom:2px solid #7eb3e8;' : '' }}">
            Usuarios
        </a>
        @endif
        @endauth
    </div>

    @auth
    <div class="navbar-user">
        <span>{{ Auth::user()->usr_nombre }}</span>
        <span class="badge">{{ Auth::user()->rol->rol_nombre ?? 'Usuario' }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Cerrar sesión</button>
        </form>
    </div>
    @endauth
</nav>

{{-- Contenido principal --}}
<div class="container">

    {{-- Mensajes de sesión --}}
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    @yield('content')
</div>

@stack('scripts')
</body>
</html>
