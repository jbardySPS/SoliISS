<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoSol extends Model
{
    protected $table      = 'SOLIISS.TIPOS_SOL';
    protected $primaryKey = 'tipo_id';
    public    $timestamps = false;

    protected $fillable = ['tipo_id', 'tipo_nombre', 'tipo_desc', 'tipo_icono'];

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'sol_tipo_id', 'tipo_id');
    }
}
