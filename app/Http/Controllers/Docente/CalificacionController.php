<?php
namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\Tutor;
use Illuminate\Http\Request;

class CalificacionController extends Controller
{
    public function store(Request $request, Proyecto $proyecto)
    {
        $request->validate([
            'calificacion' => 'required|numeric|min:0|max:100',
        ]);

        // validar que el docente sea el tutor del proyecto
        $tutor = Tutor::where('id_usuario', auth()->id())->firstOrFail();
        if ($proyecto->id_tutor !== $tutor->id_tutor) {
            abort(403, 'No autorizado');
        }

        $proyecto->calificacion = $request->calificacion;
        // si quieres marcar aprobado al calificar:
        $proyecto->estado = 'Aprobado';
        $proyecto->fecha_aprobacion = now();
        $proyecto->save();

        return back()->with('success', 'Proyecto calificado.');
    }
}
