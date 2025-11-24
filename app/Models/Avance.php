<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avance extends Model
{
    use HasFactory;

    protected $table = 'avances';
    protected $primaryKey = 'id_avance';

    protected $fillable = [
        'id_asignacion',
        'id_modulo',
        'id_usuario',
        'titulo',
        'descripcion',
        'path',
    ];

    public function usuario()
    {
        // usuario que subió el avance (estudiante)
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }

    public function asignacion()
    {
        // asignación a la que pertenece el avance
        return $this->belongsTo(AsignacionProyecto::class, 'id_asignacion', 'id_asignacion');
    }

    public function modulo()
    {
       
        return $this->belongsTo(Modulo::class, 'id_modulo', 'id_modulo');
    }
    public function correcciones()
    {
        return $this->hasMany(Correccion::class, 'id_avance', 'id_avance')
                    ->with('tutor.usuario')
                    ->latest();
    }
}   
