<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsignacionProyecto extends Model
{
    use HasFactory;

    protected $table = 'asignacion_proyecto';
    protected $primaryKey = 'id_asignacion';

    protected $fillable = [
        'id_usuario','titulo_proyecto','id_tutor','id_estudiante',
        'id_carrera','id_programa','fecha_asignacion','estado','observacion'
    ];

    // --- Relaciones básicas
    public function usuario()   { return $this->belongsTo(User::class, 'id_usuario','id'); }
    public function tutor()     { return $this->belongsTo(Tutor::class,'id_tutor','id_tutor'); }
    public function estudiante(){ return $this->belongsTo(Estudiante::class,'id_estudiante','id_estudiante'); }
    public function carrera()   { return $this->belongsTo(Carrera::class,'id_carrera','id_carrera'); }
    public function programa()  { return $this->belongsTo(Programa::class,'id_programa','id_programa'); }

    // --- Seguimiento por asignación
    public function seguimientos(){ return $this->hasMany(Seguimiento::class,'id_asignacion','id_asignacion'); }
    public function modulos() {
    return $this->hasMany(\App\Models\Modulo::class, 'id_asignacion', 'id_asignacion')
                ->orderBy('id_modulo', 'asc');
}
public function avances() {
    return $this->hasMany(\App\Models\Avance::class, 'id_asignacion', 'id_asignacion')
                ->with('usuario')
                ->latest();
}
public function correcciones() {
    return $this->hasMany(\App\Models\Correccion::class, 'id_asignacion', 'id_asignacion')
                ->with(['tutor.usuario'])
                ->latest();
}


}