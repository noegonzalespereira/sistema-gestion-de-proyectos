<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tribunal extends Model
{
    use HasFactory;
     protected $table = 'tribunales';
     protected $primaryKey="id_tribunal";
    protected $fillable = ['nombre', 'email'];

    // Relación con Proyectos
    public function proyectos()
    {
        return $this->hasMany(Proyecto::class);
    }
}
