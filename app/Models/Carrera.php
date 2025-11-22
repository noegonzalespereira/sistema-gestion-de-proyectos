<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    use HasFactory;
     protected $table = 'carreras';
     protected $primaryKey = 'id_carrera'; 
     public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['nombre', 'sigla', 'id_institucion'];

    // Relación con Institución
    public function institucion()
    {
        return $this->belongsTo(Institucion::class,'id_institucion', 'id_institucion');
    }

    // Relación con Estudiantes
    public function estudiantes()
    {
        return $this->hasMany(Estudiante::class);
    }

    // Relación con Proyectos
    public function proyectos()
    {
        return $this->hasMany(Proyecto::class);
    }
}

