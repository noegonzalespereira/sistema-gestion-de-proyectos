<?php 
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ModuloMaterial extends Model {
    protected $table = 'modulo_materiales';
    protected $primaryKey = 'id_material';
    protected $fillable = ['id_modulo','tipo','titulo','url','path'];

    public function modulo(){ return $this->belongsTo(Modulo::class,'id_modulo'); }
}