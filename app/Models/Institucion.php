<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institucion extends Model
{
    use HasFactory;
     protected $table = 'institucion';
     protected $primaryKey = 'id_institucion'; 
    protected $fillable = ['nombre', 'sigla', 'descripcion'];

    // Relación con Carreras
    public function carreras()
    {
        return $this->hasMany(Carrera::class,'id_institucion','id_institucion');
    }
}

