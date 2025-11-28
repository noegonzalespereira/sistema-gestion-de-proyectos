<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaltasModulo extends Model
{
    protected $table = 'faltas_modulo';
    protected $primaryKey = 'id_falta';

    protected $fillable = [
        'id_asignacion',
        'id_modulo',
        'id_estudiante',
        'fecha_limite_original',
        'motivo',
        'bloqueado',
        'rehabilitado',
        'nueva_fecha_limite',
    ];

    public function modulo() {
        return $this->belongsTo(Modulo::class, 'id_modulo', 'id_modulo');
    }

    public function asignacion() {
        return $this->belongsTo(AsignacionProyecto::class, 'id_asignacion', 'id_asignacion');
    }
}
