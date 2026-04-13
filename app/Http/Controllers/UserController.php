<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    // ── INDEX ────────────────────────────────────────────────────────────────
    public function index(Request $request): View
    {
        $query = User::with('rol')->orderBy('usr_nombre');

        if ($request->filled('rol')) {
            $query->where('usr_rol_id', (int) $request->rol);
        }

        if ($request->filled('estado')) {
            $query->where('usr_activo', $request->estado === 'activo' ? 1 : 0);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('usr_nombre', 'like', "%{$q}%")
                    ->orWhere('usr_email', 'like', "%{$q}%");
            });
        }

        $usuarios = $query->paginate(20)->withQueryString();
        $roles    = Rol::orderBy('rol_id')->get();

        return view('admin.usuarios.index', compact('usuarios', 'roles'));
    }

    // ── CREATE ───────────────────────────────────────────────────────────────
    public function create(): View
    {
        $roles = Rol::orderBy('rol_id')->get();
        return view('admin.usuarios.create', compact('roles'));
    }

    // ── STORE ────────────────────────────────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'usr_nombre'   => ['required', 'string', 'max:100'],
            'usr_email'    => ['required', 'email', 'max:100', Rule::unique(User::class, 'usr_email')],
            'usr_password' => ['required', Password::min(8)],
            'usr_rol_id'   => ['required', 'integer', 'in:1,2,3,4'],
            'usr_area'     => ['nullable', 'string', 'max:100'],
            'usr_activo'   => ['boolean'],
        ], [
            'usr_nombre.required'   => 'El nombre es obligatorio.',
            'usr_email.required'    => 'El email es obligatorio.',
            'usr_email.unique'      => 'Ya existe un usuario con ese email.',
            'usr_password.required' => 'La contraseña es obligatoria.',
            'usr_rol_id.required'   => 'Seleccioná un rol.',
        ]);

        User::create([
            'usr_nombre'   => $datos['usr_nombre'],
            'usr_email'    => $datos['usr_email'],
            'usr_password' => Hash::make($datos['usr_password']),
            'usr_rol_id'   => $datos['usr_rol_id'],
            'usr_area'     => $datos['usr_area'] ?? null,
            'usr_activo'   => $request->boolean('usr_activo', true) ? 1 : 0,
        ]);

        return redirect()->route('admin.usuarios.index')
                         ->with('status', "Usuario {$datos['usr_nombre']} creado correctamente.");
    }

    // ── EDIT ─────────────────────────────────────────────────────────────────
    public function edit(User $usuario): View
    {
        $roles = Rol::orderBy('rol_id')->get();
        return view('admin.usuarios.edit', compact('usuario', 'roles'));
    }

    // ── UPDATE ───────────────────────────────────────────────────────────────
    public function update(Request $request, User $usuario): RedirectResponse
    {
        $datos = $request->validate([
            'usr_nombre'   => ['required', 'string', 'max:100'],
            'usr_email'    => ['required', 'email', 'max:100', Rule::unique(User::class, 'usr_email')->ignore($usuario->usr_id, 'usr_id')],
            'usr_password' => ['nullable', Password::min(8)],
            'usr_rol_id'   => ['required', 'integer', 'in:1,2,3,4'],
            'usr_area'     => ['nullable', 'string', 'max:100'],
            'usr_activo'   => ['boolean'],
        ], [
            'usr_nombre.required' => 'El nombre es obligatorio.',
            'usr_email.required'  => 'El email es obligatorio.',
            'usr_email.unique'    => 'Ya existe otro usuario con ese email.',
            'usr_rol_id.required' => 'Seleccioná un rol.',
        ]);

        $actualizar = [
            'usr_nombre' => $datos['usr_nombre'],
            'usr_email'  => $datos['usr_email'],
            'usr_rol_id' => $datos['usr_rol_id'],
            'usr_area'   => $datos['usr_area'] ?? null,
            'usr_activo' => $request->boolean('usr_activo') ? 1 : 0,
        ];

        if (! empty($datos['usr_password'])) {
            $actualizar['usr_password'] = Hash::make($datos['usr_password']);
        }

        $usuario->update($actualizar);

        return redirect()->route('admin.usuarios.index')
                         ->with('status', "Usuario {$datos['usr_nombre']} actualizado correctamente.");
    }

    // ── TOGGLE ACTIVO ────────────────────────────────────────────────────────
    public function toggleActivo(User $usuario): RedirectResponse
    {
        $nuevo = $usuario->usr_activo ? 0 : 1;
        $usuario->update(['usr_activo' => $nuevo]);

        $msg = $nuevo ? 'activado' : 'desactivado';
        return back()->with('status', "Usuario {$usuario->usr_nombre} {$msg}.");
    }
}
