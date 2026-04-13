<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoSol extends Model
{
    protected $table      = 'SOLIISS.ESTADOS_SOL';
    protected $primaryKey = 'est_id';
    public    $timestamps = false;

    protected $fillable = ['est_id', 'est_nombre', 'est_color', 'est_orden'];

    protected $casts = ['est_id' => 'integer', 'est_orden' => 'integer'];

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'sol_estado_id', 'est_id');
    }
}
