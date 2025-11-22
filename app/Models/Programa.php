<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programa extends Model
{
    use HasFactory;
     protected $table = 'programas';
     protected $primaryKey='id_programa';
    protected $fillable = ['nombre', 'descripcion'];

    // Relación con Proyectos
    public function proyectos()
    {
        return $this->hasMany(Proyecto::class);
    }
}

