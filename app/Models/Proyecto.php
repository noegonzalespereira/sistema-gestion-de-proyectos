<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    use HasFactory;
     protected $table = 'proyectos';
    protected $fillable = [
        'titulo', 'resumen', 'id_programa', 'id_carrera', 'id_estudiante', 
        'id_tutor', 'id_tribunal', 'id_usuario', 'anio', 'fecha_defensa', 
        'fecha_aprobacion', 'calificacion', 'link_pdf', 'fecha_registro', 'estado'
    ];

    // Relación con Usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    // Relación con Carrera
    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'id_carrera','id_carrera');
    }

    // Relación con Tribunal
    public function tribunal()
    {
        return $this->belongsTo(Tribunal::class, 'id_tribunal','id_tribunal');
    }

    // Relación con Tutor
    public function tutor()
    {
        return $this->belongsTo(Tutor::class, 'id_tutor','id_tutor');
    }

    // Relación con Estudiante
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'id_estudiante','id_estudiante');
    }

    // Relación con Programa
    public function programa()
    {
        return $this->belongsTo(Programa::class, 'id_programa','id_programa');
    }

    // Relación con AsignacionProyecto
    public function asignacion()
    {
        return $this->hasMany(AsignacionProyecto::class, 'id_proyecto');
    }

    // Relación con Seguimiento
    public function seguimientos()
    {
        return $this->hasMany(Seguimiento::class, 'id_proyecto');
    }
    public function modulos() {
    return $this->hasMany(\App\Models\Modulo::class, 'id_proyecto', 'id_proyecto')
                ->with('materiales')
                ->latest();
    }
    public function avances() {
        return $this->hasMany(\App\Models\Avance::class, 'id_proyecto', 'id_proyecto')
                    ->with('usuario')
                    ->latest();
    }
    public function correcciones() {
        return $this->hasMany(\App\Models\Correccion::class, 'id_proyecto', 'id_proyecto')
                    ->with(['tutor.usuario'])
                    ->latest();
    }
}


