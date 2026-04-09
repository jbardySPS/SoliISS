<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión — SOLI-ISS</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            font-size: 14px;
            background: #f0f2f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-wrapper {
            width: 100%;
            max-width: 380px;
            padding: 16px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 28px;
        }
        .login-header .logo {
            font-size: 28px;
            font-weight: 700;
            color: #1a3a5c;
            letter-spacing: 1px;
        }
        .login-header .logo span { color: #2e6da4; }
        .login-header .subtitle {
            margin-top: 6px;
            font-size: 12px;
            color: #777;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0,0,0,.1);
            padding: 32px 28px;
        }

        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #444;
            font-size: 13px;
        }
        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #d0d5dd;
            border-radius: 5px;
            font-size: 14px;
            color: #333;
            transition: border-color .15s;
            outline: none;
        }
        .form-group input:focus {
            border-color: #2e6da4;
            box-shadow: 0 0 0 3px rgba(46,109,164,.12);
        }
        .form-group input.is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 12px;
            margin-top: 4px;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 22px;
        }
        .form-check input { width: 15px; height: 15px; cursor: pointer; }
        .form-check label { font-size: 13px; color: #555; cursor: pointer; }

        .btn-submit {
            width: 100%;
            padding: 10px;
            background: #1a3a5c;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s;
        }
        .btn-submit:hover { background: #2e6da4; }

        .login-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 11px;
            color: #aaa;
        }
    </style>
</head>
<body>

<div class="login-wrapper">

    <div class="login-header">
        <div class="logo">SOLI-<span>ISS</span></div>
        <div class="subtitle">Instituto de Seguridad Social — La Pampa</div>
    </div>

    <div class="card">

        {{-- Error general (de sesión) --}}
        @if(session('error'))
            <div style="background:#f8d7da;color:#721c24;padding:9px 12px;border-radius:4px;margin-bottom:18px;font-size:13px;">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf

            {{-- Email --}}
            <div class="form-group">
                <label for="email">Email institucional</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    autocomplete="email"
                    autofocus
                    class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                    placeholder="usuario@iss.lapampa.gov.ar"
                >
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Contraseña --}}
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    autocomplete="current-password"
                    class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                >
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Recordarme --}}
            <div class="form-check">
                <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember">Recordar mi sesión</label>
            </div>

            <button type="submit" class="btn-submit">Ingresar</button>

        </form>
    </div>

    <div class="login-footer">
        Uso exclusivo del personal de ISS La Pampa
    </div>

</div>

</body>
</html>
