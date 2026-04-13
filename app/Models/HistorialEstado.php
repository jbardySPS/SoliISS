<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialEstado extends Model
{
    protected $table      = 'SOLIISS.HISTORIAL_ESTADOS';
    protected $primaryKey = 'his_id';
    public    $timestamps = false;

    protected $fillable = [
        'his_sol_id', 'his_est_anterior', 'his_est_nuevo',
        'his_usr_id', 'his_comentario', 'his_fecha',
    ];

    protected $casts = [
        'his_est_anterior' => 'integer',
        'his_est_nuevo'    => 'integer',
        'his_usr_id'       => 'integer',
        'his_fecha'        => 'datetime',
    ];

    public function solicitud() { return $this->belongsTo(Solicitud::class, 'his_sol_id', 'sol_id'); }
    public function usuario()   { return $this->belongsTo(User::class,      'his_usr_id', 'usr_id'); }

    public function estadoAnterior()
    {
        return $this->belongsTo(EstadoSol::class, 'his_est_anterior', 'est_id');
    }

    public function estadoNuevo()
    {
        return $this->belongsTo(EstadoSol::class, 'his_est_nuevo', 'est_id');
    }
}
