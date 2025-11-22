<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    use HasFactory;
    protected $table = 'estudiantes';
    protected $primaryKey = 'id_estudiante';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['id_usuario', 'ci', 'id_carrera'];

    // Relación con Usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario','id');
    }

    // Relación con Carrera
    public function carrera()
    {
        return $this->belongsTo(Carrera::class,'id_carrera', 'id_carrera');
    }

    // Relación con Proyectos
    public function proyectos()
    {
        return $this->hasMany(Proyecto::class);
    }
}

