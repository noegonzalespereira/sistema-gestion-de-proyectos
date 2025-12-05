<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\AsignacionProyecto;
use App\Models\Tutor;
use App\Models\Modulo;
use Illuminate\Http\Request;

class AsignacionController extends Controller
{
    // Lista de proyectos del tutor logueado
    public function index(Request $request)
    {
        $tutor = Tutor::where('id_usuario', auth()->id())->firstOrFail();

        $asignaciones = AsignacionProyecto::where('id_tutor', $tutor->id_tutor)
            ->with([
                'estudiante.usuario',
                'carrera',
                'programa',
                'modulos',
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        $idsAsignacionesTutor = AsignacionProyecto::where('id_tutor', $tutor->id_tutor)
            ->pluck('id_asignacion');

        $modulosBase = Modulo::whereIn('id_asignacion', $idsAsignacionesTutor)
            ->whereNull('fecha_limite')
            ->orderBy('id_modulo')
            ->get();
        

        return view('docente.asignaciones', compact('asignaciones','modulosBase'));
    }

    // Pantalla completa para gestionar UNA asignación
    public function show(AsignacionProyecto $asignacion)
    {
        $tutor = Tutor::where('id_usuario', auth()->id())->firstOrFail();

        if ($asignacion->id_tutor !== $tutor->id_tutor) {
            abort(403);
        }

        $asignacion->load([
            'estudiante.usuario',
            'carrera',
            'programa',
            'modulos.materiales',
            'modulos.avances.correcciones',
        ]);

        return view('docente.asignaciones_show', compact('asignacion'));
    }
}
