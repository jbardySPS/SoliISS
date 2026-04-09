<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    // ── Mostrar formulario de login ─────────────────────────────────────────
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->intended(route('dashboard'));
        }

        return view('auth.login');
    }

    // ── Procesar login ──────────────────────────────────────────────────────
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => 'El email es obligatorio.',
            'email.email'       => 'El formato del email no es válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        // Rate limiting: máximo 5 intentos por minuto por IP + email
        $this->checkRateLimit($request);

        $credenciales = [
            'usr_email'  => $request->email,
            'password'   => $request->password,   // Laravel mapea "password" → getAuthPassword()
             ];

        if (Auth::attempt($credenciales, $request->boolean('remember'))) {
            $request->session()->regenerate();
            RateLimiter::clear($this->rateLimitKey($request));

            return redirect()->intended(route('dashboard'));
        }

        // Intentos fallidos: incrementar contador
        RateLimiter::hit($this->rateLimitKey($request));

        throw ValidationException::withMessages([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ]);
    }

    // ── Logout ──────────────────────────────────────────────────────────────
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Sesión cerrada correctamente.');
    }

    // ── Rate Limiting ───────────────────────────────────────────────────────
    protected function rateLimitKey(Request $request): string
    {
        return Str::transliterate(
            Str::lower($request->input('email')) . '|' . $request->ip()
        );
    }

    protected function checkRateLimit(Request $request): void
    {
        $key = $this->rateLimitKey($request);

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $segundos = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'email' => "Demasiados intentos. Podés volver a intentar en {$segundos} segundos.",
            ]);
        }
    }
}
