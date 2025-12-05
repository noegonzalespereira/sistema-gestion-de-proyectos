<?php

namespace App\Http\Controllers;


use App\Models\AsignacionProyecto;

class DocenteController extends Controller
{
    public function asignaciones()
    {
        $usuario = auth()->user();

        // es docente?
        $tutor = \App\Models\Tutor::where('id_usuario', $usuario->id)->first();

        if (!$tutor) {
            return view('docente.asignaciones', [
                'asignaciones'       => collect(),
                'proyectosRevision'  => collect(), // ya no se usan, pero los dejamos vacíos
                'proyectosAprobados' => collect(),
            ]);
        }

        $asignaciones = AsignacionProyecto::with([
                'estudiante.usuario',
                'carrera',
                'programa',
                'modulos.materiales',  
                'modulos.correcciones.tutor.usuario',
                'modulos.avances.correcciones.tutor.usuario',
            ])
            ->where('id_tutor', $tutor->id_tutor)
            ->orderByDesc('fecha_asignacion')
            ->paginate(10);
        $proyectosRevision  = collect();
        $proyectosAprobados = collect();  

        $modulosBase = \App\Models\Modulo::whereIn(
            'id_asignacion',
            \App\Models\AsignacionProyecto::where('id_tutor', $tutor->id_tutor)->pluck('id_asignacion')
            )
            ->whereNull('fecha_limite')
            ->with('materiales')
            ->orderBy('id_modulo')
            ->get();
        

        return view('docente.asignaciones', compact('asignaciones','proyectosRevision','proyectosAprobados','modulosBase'));
    }
}
