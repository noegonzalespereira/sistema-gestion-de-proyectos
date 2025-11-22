<?php 
namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\AsignacionProyecto;
use App\Models\Correccion;
use App\Models\Tutor;
use Illuminate\Http\Request;

class CorreccionController extends Controller
{
    public function store(Request $request, AsignacionProyecto $asignacion) {
        $request->validate([
            'comentario'   => 'nullable|string',
            'fecha_limite' => 'nullable|date',
            'archivo'      => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:5120',
            'id_modulo'    => 'nullable|integer|exists:modulos,id_modulo', 
        ]);

        $tutor = Tutor::where('id_usuario', auth()->id())->firstOrFail();

        $corr = new Correccion([
            'id_asignacion' => $asignacion->id_asignacion,
            'id_modulo'     => $request->id_modulo,
            'id_tutor'      => $tutor->id_tutor,
            'comentario'    => $request->comentario,
            'fecha_limite'  => $request->fecha_limite,
        ]);

        if ($request->hasFile('archivo')) {
            $corr->path = $request->file('archivo')->store('correcciones','public');
        }
        $corr->save();

        return back()->with('success','Corrección enviada al estudiante.');
    }
}
