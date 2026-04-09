<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // ── Tabla y clave primaria ──────────────────────────────────────────────
    // IMPORTANTE: PDO::CASE_LOWER convierte todas las columnas a minúsculas,
    // por eso todos los nombres de columna se definen en minúsculas aquí.
    protected $table      = 'SOLIISS.USUARIOS';
    protected $primaryKey = 'usr_id';

    // ── Columnas de timestamp (Laravel las maneja automáticamente) ──────────
    const CREATED_AT = 'usr_creado';
    const UPDATED_AT = 'usr_actualizado';

    // ── Token "recordarme" ──────────────────────────────────────────────────
    protected $rememberTokenName = 'usr_recuerdame';

    // ── Columnas asignables en masa ─────────────────────────────────────────
    protected $fillable = [
        'usr_nombre',
        'usr_email',
        'usr_password',
        'usr_area',
        'usr_rol_id',
        'usr_activo',
    ];

    // ── Columnas ocultas al serializar (nunca exponer al frontend) ──────────
    protected $hidden = [
        'usr_password',
        'usr_recuerdame',
    ];

    // ── Casting de tipos ────────────────────────────────────────────────────
    protected $casts = [
        'usr_activo'      => 'boolean',
        'usr_rol_id'      => 'integer',
        'usr_creado'      => 'datetime',
        'usr_actualizado' => 'datetime',
    ];

    // ── Laravel necesita conocer el campo de email y password ───────────────
    // Se indica en config/auth.php, pero el model también debe tener
    // el método getAuthPassword() apuntando a la columna real.

        public function getAuthPasswordName(): string
    {
        return 'usr_password';
    }
    
    public function getAuthPassword(): string
    {
        return $this->usr_password ?? '';
    }
    // ── Relación con Rol ────────────────────────────────────────────────────
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'usr_rol_id', 'rol_id');
    }

    // ── Helpers de rol (usados en vistas y middleware) ──────────────────────
    public function esSolicitante(): bool  { return $this->usr_rol_id === 1; }
    public function esSupervisor(): bool   { return $this->usr_rol_id === 2; }
    public function esOperador(): bool     { return $this->usr_rol_id === 3; }
    public function esAdmin(): bool        { return $this->usr_rol_id === 4; }

    public function tieneRol(int|array $rol): bool
    {
        return in_array($this->usr_rol_id, (array) $rol);
    }

    // ── Scope: solo usuarios activos ────────────────────────────────────────
    public function scopeActivos($query)
    {
        return $query->where('usr_activo', 1);
    }
}
