<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    protected $table      = 'SOLIISS.COMENTARIOS';
    protected $primaryKey = 'com_id';
    public    $timestamps = false;

    protected $fillable = [
        'com_sol_id', 'com_usr_id', 'com_texto', 'com_interno', 'com_fecha',
    ];

    protected $casts = [
        'com_interno' => 'boolean',
        'com_fecha'   => 'datetime',
    ];

    public function solicitud() { return $this->belongsTo(Solicitud::class, 'com_sol_id', 'sol_id'); }
    public function usuario()   { return $this->belongsTo(User::class,      'com_usr_id', 'usr_id'); }
}
