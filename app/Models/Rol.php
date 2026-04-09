<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    // IMPORTANTE: PDO::CASE_LOWER convierte todas las columnas a minúsculas.
    protected $table      = 'SOLIISS.ROLES';
    protected $primaryKey = 'rol_id';
    public    $timestamps = false;   // ROLES no tiene columnas de timestamp

    protected $fillable = ['rol_id', 'rol_nombre', 'rol_desc'];

    // Constantes para usar en el código sin números mágicos
    const SOLICITANTE = 1;
    const SUPERVISOR  = 2;
    const OPERADOR    = 3;
    const ADMIN       = 4;

    public function usuarios()
    {
        return $this->hasMany(User::class, 'usr_rol_id', 'rol_id');
    }
}
