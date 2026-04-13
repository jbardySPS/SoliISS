<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaDest extends Model
{
    protected $table      = 'SOLIISS.AREAS_DEST';
    protected $primaryKey = 'area_id';
    public    $timestamps = false;

    protected $fillable = ['area_id', 'area_nombre', 'area_desc'];

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'sol_area_dest_id', 'area_id');
    }
}
