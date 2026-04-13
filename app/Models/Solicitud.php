<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Solicitud extends Model
{
    // IMPORTANTE: PDO::CASE_LOWER convierte todas las columnas a minúsculas.
    protected $table      = 'SOLIISS.SOLICITUDES';
    protected $primaryKey = 'sol_id';

    const CREATED_AT = 'sol_creado';
    const UPDATED_AT = 'sol_actualizado';

    // ── Estados del ciclo de vida ───────────────────────────────────────────
    const BORRADOR   = 'borrador';
    const PENDIENTE  = 'pendiente';
    const APROBADA   = 'aprobada';
    const RECHAZADA  = 'rechazada';
    const EN_PROCESO = 'en_proceso';
    const COMPLETADA = 'completada';
    const CANCELADA  = 'cancelada';

    // ── Prioridades ─────────────────────────────────────────────────────────
    const PRIORIDAD_BAJA  = 'baja';
    const PRIORIDAD_MEDIA = 'media';
    const PRIORIDAD_ALTA  = 'alta';

    // ── Tipos de solicitud ──────────────────────────────────────────────────
    const TIPOS = [
        'licencia_medica'   => 'Licencia médica',
        'vacaciones'        => 'Vacaciones',
        'permiso_especial'  => 'Permiso especial',
        'materiales'        => 'Materiales / Insumos',
        'soporte_tecnico'   => 'Soporte técnico',
        'capacitacion'      => 'Capacitación',
        'otro'              => 'Otro',
    ];

    protected $fillable = [
        'sol_usr_id',
        'sol_tipo',
        'sol_descripcion',
        'sol_prioridad',
        'sol_estado',
        'sol_supervisor_id',
        'sol_obs_supervisor',
        'sol_operador_id',
        'sol_obs_operador',
    ];

    protected $casts = [
        'sol_id'            => 'integer',
        'sol_usr_id'        => 'integer',
        'sol_supervisor_id' => 'integer',
        'sol_operador_id'   => 'integer',
        'sol_creado'        => 'datetime',
        'sol_actualizado'   => 'datetime',
    ];

    // ── Relaciones ──────────────────────────────────────────────────────────
    public function solicitante()
    {
        return $this->belongsTo(User::class, 'sol_usr_id', 'usr_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'sol_supervisor_id', 'usr_id');
    }

    public function operador()
    {
        return $this->belongsTo(User::class, 'sol_operador_id', 'usr_id');
    }

    // ── Helpers de estado ───────────────────────────────────────────────────
    public function esBorrador(): bool   { return $this->sol_estado === self::BORRADOR; }
    public function esPendiente(): bool  { return $this->sol_estado === self::PENDIENTE; }
    public function esAprobada(): bool   { return $this->sol_estado === self::APROBADA; }
    public function esRechazada(): bool  { return $this->sol_estado === self::RECHAZADA; }
    public function esEnProceso(): bool  { return $this->sol_estado === self::EN_PROCESO; }
    public function esCompletada(): bool { return $this->sol_estado === self::COMPLETADA; }
    public function esCancelada(): bool  { return $this->sol_estado === self::CANCELADA; }

    public function estaTerminada(): bool
    {
        return in_array($this->sol_estado, [self::COMPLETADA, self::CANCELADA, self::RECHAZADA]);
    }

    // ── Número legible (SOL-2026-00001) ─────────────────────────────────────
    public function getNumeroAttribute(): string
    {
        $anio = $this->sol_creado ? $this->sol_creado->format('Y') : date('Y');
        return 'SOL-' . $anio . '-' . str_pad($this->sol_id, 5, '0', STR_PAD_LEFT);
    }

    // ── Label y color del estado (para vistas) ──────────────────────────────
    public function getEstadoLabelAttribute(): string
    {
        return match($this->sol_estado) {
            self::BORRADOR   => 'Borrador',
            self::PENDIENTE  => 'Pendiente',
            self::APROBADA   => 'Aprobada',
            self::RECHAZADA  => 'Rechazada',
            self::EN_PROCESO => 'En proceso',
            self::COMPLETADA => 'Completada',
            self::CANCELADA  => 'Cancelada',
            default          => ucfirst($this->sol_estado),
        };
    }

    public function getEstadoColorAttribute(): string
    {
        return match($this->sol_estado) {
            self::BORRADOR   => '#6c757d',
            self::PENDIENTE  => '#e67e22',
            self::APROBADA   => '#2e6da4',
            self::EN_PROCESO => '#8e44ad',
            self::COMPLETADA => '#27ae60',
            self::RECHAZADA  => '#c0392b',
            self::CANCELADA  => '#95a5a6',
            default          => '#333',
        };
    }

    public function getPrioridadLabelAttribute(): string
    {
        return match($this->sol_prioridad) {
            self::PRIORIDAD_ALTA  => 'Alta',
            self::PRIORIDAD_MEDIA => 'Media',
            self::PRIORIDAD_BAJA  => 'Baja',
            default               => ucfirst($this->sol_prioridad),
        };
    }

    public function getPrioridadColorAttribute(): string
    {
        return match($this->sol_prioridad) {
            self::PRIORIDAD_ALTA  => '#c0392b',
            self::PRIORIDAD_MEDIA => '#e67e22',
            self::PRIORIDAD_BAJA  => '#27ae60',
            default               => '#333',
        };
    }

    public function getTipoLabelAttribute(): string
    {
        return self::TIPOS[$this->sol_tipo] ?? ucfirst($this->sol_tipo);
    }

    // ── Scopes ──────────────────────────────────────────────────────────────
    public function scopeDelUsuario(Builder $query, int $usrId): Builder
    {
        return $query->where('sol_usr_id', $usrId);
    }

    public function scopeEnEstado(Builder $query, string|array $estado): Builder
    {
        return $query->whereIn('sol_estado', (array) $estado);
    }

    public function scopePendientesRevision(Builder $query): Builder
    {
        return $query->where('sol_estado', self::PENDIENTE);
    }

    public function scopeParaOperador(Builder $query): Builder
    {
        return $query->whereIn('sol_estado', [self::APROBADA, self::EN_PROCESO]);
    }
}
