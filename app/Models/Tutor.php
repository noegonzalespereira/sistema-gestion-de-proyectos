<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tutor extends Model
{
    use HasFactory;
    protected $table = 'tutores';
    protected $primaryKey="id_tutor";

    protected $fillable = ['id_usuario', 'item'];

    // Relación con Usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    // Relación con Proyectos
    public function proyectos()
    {
        return $this->hasMany(Proyecto::class);
    }
}

