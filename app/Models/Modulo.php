<?php
namespace App\Models;
use App\Models\AsignacionProyecto;
use App\Models\ModuloMaterial;
use App\Models\Avance;

use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    protected $table = 'modulos';
    protected $primaryKey = 'id_modulo';

    protected $fillable = [
        'id_asignacion',
        'titulo',
        'descripcion',
        'estado',
        'calificacion',
        'fecha_limite',
    ];
    protected $casts = [
    'fecha_limite' => 'date',
];


    public function asignacion()
    {
        return $this->belongsTo(AsignacionProyecto::class, 'id_asignacion', 'id_asignacion');
    }

    public function materiales()
    {
        return $this->hasMany(ModuloMaterial::class, 'id_modulo','id_modulo');
    }
    public function avances()
    {
        return $this->hasMany(Avance::class, 'id_modulo', 'id_modulo');
                    
                    
    }
    public function correcciones()
    {
        return $this->hasMany(Correccion::class, 'id_modulo', 'id_modulo');
    }


}
