<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Correccion extends Model
{
    use HasFactory;

    protected $table = 'correcciones';
    protected $primaryKey = 'id_correccion'; // o el que tengas

    protected $fillable = [
        'id_asignacion',
        'id_modulo',      // 👈 nuevo
        'id_tutor',
        'comentario',
        'fecha_limite',
        'path',
    ];
    

    public function asignacion()
    {
        return $this->belongsTo(AsignacionProyecto::class, 'id_asignacion', 'id_asignacion');
    }

    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'id_modulo', 'id_modulo');
    }

    public function tutor()
    {
        return $this->belongsTo(Tutor::class, 'id_tutor', 'id_tutor');
    }
}
