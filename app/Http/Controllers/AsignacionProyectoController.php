<?php

namespace App\Http\Controllers;

use App\Models\AsignacionProyecto;
use App\Models\Carrera;
use App\Models\Programa;
use App\Models\Tutor;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AsignacionProyectoController extends Controller
{
    public function index(Request $request)
    {
        // Filtros simples (opcionales)
        $q = AsignacionProyecto::with([
            'usuario',
            'tutor.usuario',      // si tu Tutor tiene ->usuario
            'estudiante.usuario', // si tu Estudiante tiene ->usuario
            'carrera',
            'programa',
        ])->orderBy('created_at', 'desc');

        if ($request->filled('estado')) {
            $q->where('estado', $request->estado);
        }
        if ($request->filled('id_carrera')) {
            $q->where('id_carrera', $request->id_carrera);
        }
        if ($request->filled('id_programa')) {
            $q->where('id_programa', $request->id_programa);
        }

        $asignaciones = $q->get();

        // Para selects del modal
        $carreras    = Carrera::orderBy('nombre')->get();
        $programas   = Programa::orderBy('nombre')->get();
        $tutores     = Tutor::with('usuario')->orderBy('id_tutor','desc')->get();
        $estudiantes = Estudiante::with('usuario')->orderBy('id_estudiante','desc')->get();

        return view('admin.asignaciones.index', compact(
            'asignaciones', 'carreras', 'programas', 'tutores', 'estudiantes'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo_proyecto' => ['nullable','string','max:255'],
            'id_tutor'        => ['nullable','exists:tutores,id_tutor'],
            'id_estudiante'   => ['nullable','exists:estudiantes,id_estudiante'],
            'id_carrera'      => ['nullable','exists:carreras,id_carrera'],
            'id_programa'     => ['nullable','exists:programas,id_programa'],
            'fecha_asignacion'=> ['nullable','date'],
            'estado'          => ['nullable', Rule::in(['Asignado','Aprobado','Observado'])],
            'observacion'     => ['nullable','string'],
        ]);
        $idCarrera = $request->id_carrera;
        if (!$idCarrera && $request->id_estudiante) {
            $est = Estudiante::find($request->id_estudiante);
            if ($est && $est->id_carrera) {
                $idCarrera = $est->id_carrera;
            }
        }

        AsignacionProyecto::create([
            'id_usuario'       => auth()->id(),
            'titulo_proyecto'  => $request->titulo_proyecto,
            'id_tutor'         => $request->id_tutor,
            'id_estudiante'    => $request->id_estudiante,
            'id_carrera'       => $request->id_carrera,
            'id_programa'      => $request->id_programa,
            'fecha_asignacion' => $request->fecha_asignacion,
            'estado'           => $request->estado ?: 'Observado',
            'observacion'      => $request->observacion,
        ]);
                 

        return back()->with('success', 'Asignación creada correctamente.');
    }

    public function edit(AsignacionProyecto $asignacion)
    {
        $carreras    = Carrera::orderBy('nombre')->get();
        $programas   = Programa::orderBy('nombre')->get();
        $tutores     = Tutor::with('usuario')->orderBy('id_tutor','desc')->get();
        $estudiantes = Estudiante::with('usuario')->orderBy('id_estudiante','desc')->get();

        return view('admin.asignaciones.edit', compact(
            'asignacion','carreras','programas','tutores','estudiantes'
        ));
    }

    public function update(Request $request, AsignacionProyecto $asignacion)
    {
        $request->validate([
            'titulo_proyecto' => ['nullable','string','max:255'],
            'id_tutor'        => ['nullable','exists:tutores,id_tutor'],
            'id_estudiante'   => ['nullable','exists:estudiantes,id_estudiante'],
            'id_carrera'      => ['nullable','exists:carreras,id_carrera'],
            'id_programa'     => ['nullable','exists:programas,id_programa'],
            'fecha_asignacion'=> ['nullable','date'],
            'estado'          => ['nullable', Rule::in(['Asignado','Aprobado','Observado'])],
            'observacion'     => ['nullable','string'],
        ]);

        $asignacion->update([
            'titulo_proyecto'  => $request->titulo_proyecto,
            'id_tutor'         => $request->id_tutor,
            'id_estudiante'    => $request->id_estudiante,
            'id_carrera'       => $request->id_carrera,
            'id_programa'      => $request->id_programa,
            'fecha_asignacion' => $request->fecha_asignacion,
            'estado'           => $request->estado ?: 'Observado',
            'observacion'      => $request->observacion,
        ]);

        return redirect()->route('asignaciones.index')->with('success', 'Asignación actualizada correctamente.');
    }

    public function destroy(AsignacionProyecto $asignacion)
    {
        $asignacion->delete();
        return back()->with('success','Asignación eliminada.');
    }
    
}
