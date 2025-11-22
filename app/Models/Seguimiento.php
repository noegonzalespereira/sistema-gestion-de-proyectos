<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seguimiento extends Model
{
    use HasFactory;
     protected $table = 'seguimiento_proyecto';
    protected $fillable = ['id_asignacion', 'modulo', 'descripcion', 'estado', 'observacion', 'archivo_url', 'archivo_correccion_url', 'fecha_limite'];

    // Relación con AsignacionProyecto
    public function asignacion()
    {
        return $this->belongsTo(AsignacionProyecto::class, 'id_asignacion');
    }
}

