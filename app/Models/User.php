<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;  // Asegúrate de usar el trait Notifiable
     protected $table = 'users';
    // Campos que se pueden asignar masivamente
    protected $fillable = ['name', 'email', 'password', 'rol', 'activo'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts  = [
        'email_verified_at' => 'datetime',
        'activo' => 'boolean',
    ];
    
    // Definir las relaciones
    public function proyectos()
    {
        return $this->hasMany(Proyecto::class,'id_usuario');
    }

    public function asignaciones()
    {
        return $this->hasMany(AsignacionProyecto::class,'id_usuario');
    }

    public function tribunales()
    {
        return $this->hasMany(Tribunal::class,'id_usuario');
    }
    public function tutor()
    {
        return $this->hasOne(\App\Models\Tutor::class, 'id_usuario');
    }
    public function estudiante()
    {
        return $this->hasOne(\App\Models\Estudiante::class, 'id_usuario');
    }

    
}
