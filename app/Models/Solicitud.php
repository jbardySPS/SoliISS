<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Solicitud extends Model
{
    protected $table      = 'SOLIISS.SOLICITUDES';
    protected $primaryKey = 'sol_id';

    const CREATED_AT = 'sol_fecha_creacion';
    const UPDATED_AT = 'sol_actualizado';

    // ── IDs de estado (coinciden con SOLIISS.ESTADOS_SOL) ──────────────────
    const EST_BORRADOR    = 1;
    const EST_ENVIADA     = 2;
    const EST_EN_REVISION = 3;
    const EST_APROBADA    = 4;
    const EST_RECHAZADA   = 5;
    const EST_ASIGNADA    = 6;
    const EST_EN_PROGRESO = 7;
    const EST_RESUELTA    = 8;
    const EST_CERRADA     = 9;

    // ── Prioridades ─────────────────────────────────────────────────────────
    const PRIO_BAJA  = 1;
    const PRIO_MEDIA = 2;
    const PRIO_ALTA  = 3;

    const PRIORIDADES = [
        self::PRIO_BAJA  => ['label' => 'Baja',  'color' => '#27ae60'],
        self::PRIO_MEDIA => ['label' => 'Media', 'color' => '#e67e22'],
        self::PRIO_ALTA  => ['label' => 'Alta',  'color' => '#c0392b'],
    ];

    protected $fillable = [
        'sol_numero',
        'sol_tipo_id',
        'sol_area_dest_id',
        'sol_titulo',
        'sol_descripcion',
        'sol_prioridad',
        'sol_estado_id',
        'sol_sistema',
        'sol_usr_solicita',
        'sol_usr_supervisor',
        'sol_usr_asignado',
        'sol_resolucion',
        'sol_fecha_envio',
        'sol_fecha_aprobacion',
        'sol_fecha_cierre',
    ];

    protected $casts = [
        'sol_id'              => 'integer',
        'sol_tipo_id'         => 'integer',
        'sol_area_dest_id'    => 'integer',
        'sol_prioridad'       => 'integer',
        'sol_estado_id'       => 'integer',
        'sol_usr_solicita'    => 'integer',
        'sol_usr_supervisor'  => 'integer',
        'sol_usr_asignado'    => 'integer',
        'sol_fecha_creacion'  => 'datetime',
        'sol_fecha_envio'     => 'datetime',
        'sol_fecha_aprobacion'=> 'datetime',
        'sol_fecha_cierre'    => 'datetime',
        'sol_actualizado'     => 'datetime',
    ];

    // ── Relaciones ──────────────────────────────────────────────────────────
    public function tipo()       { return $this->belongsTo(TipoSol::class,  'sol_tipo_id',       'tipo_id'); }
    public function area()       { return $this->belongsTo(AreaDest::class, 'sol_area_dest_id',  'area_id'); }
    public function estado()     { return $this->belongsTo(EstadoSol::class,'sol_estado_id',     'est_id');  }
    public function solicitante(){ return $this->belongsTo(User::class,     'sol_usr_solicita',  'usr_id');  }
    public function supervisor() { return $this->belongsTo(User::class,     'sol_usr_supervisor','usr_id');  }
    public function asignado()   { return $this->belongsTo(User::class,     'sol_usr_asignado',  'usr_id');  }

    public function historial()
    {
        return $this->hasMany(HistorialEstado::class, 'his_sol_id', 'sol_id')
                    ->orderBy('his_fecha', 'asc');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'com_sol_id', 'sol_id')
                    ->orderBy('com_fecha', 'asc');
    }

    // ── Helpers de estado ───────────────────────────────────────────────────
    public function esBorrador():   bool { return $this->sol_estado_id === self::EST_BORRADOR;    }
    public function esEnviada():    bool { return $this->sol_estado_id === self::EST_ENVIADA;     }
    public function esEnRevision(): bool { return $this->sol_estado_id === self::EST_EN_REVISION; }
    public function esAprobada():   bool { return $this->sol_estado_id === self::EST_APROBADA;    }
    public function esRechazada():  bool { return $this->sol_estado_id === self::EST_RECHAZADA;   }
    public function esAsignada():   bool { return $this->sol_estado_id === self::EST_ASIGNADA;    }
    public function esEnProgreso(): bool { return $this->sol_estado_id === self::EST_EN_PROGRESO; }
    public function esResuelta():   bool { return $this->sol_estado_id === self::EST_RESUELTA;    }
    public function esCerrada():    bool { return $this->sol_estado_id === self::EST_CERRADA;     }

    public function estaTerminada(): bool
    {
        return in_array($this->sol_estado_id, [self::EST_RECHAZADA, self::EST_CERRADA]);
    }

    public function estaActiva(): bool
    {
        return ! $this->estaTerminada();
    }

    // ── Accessors de etiqueta / color ────────────────────────────────────────
    public function getPrioridadLabelAttribute(): string
    {
        return self::PRIORIDADES[$this->sol_prioridad]['label'] ?? '—';
    }

    public function getPrioridadColorAttribute(): string
    {
        return self::PRIORIDADES[$this->sol_prioridad]['color'] ?? '#333';
    }

    // ── Generación de número legible ─────────────────────────────────────────
    public static function generarNumero(int $id): string
    {
        return 'SOL-' . date('Y') . '-' . str_pad($id, 5, '0', STR_PAD_LEFT);
    }

    // ── Scopes ──────────────────────────────────────────────────────────────
    public function scopeDelUsuario(Builder $q, int $usrId): Builder
    {
        return $q->where('sol_usr_solicita', $usrId);
    }

    public function scopeEnEstado(Builder $q, int|array $estado): Builder
    {
        return $q->whereIn('sol_estado_id', (array) $estado);
    }

    public function scopePendientesRevision(Builder $q): Builder
    {
        return $q->whereIn('sol_estado_id', [self::EST_ENVIADA, self::EST_EN_REVISION]);
    }

    public function scopeParaOperador(Builder $q): Builder
    {
        return $q->whereIn('sol_estado_id', [
            self::EST_APROBADA, self::EST_ASIGNADA, self::EST_EN_PROGRESO,
        ]);
    }
}
