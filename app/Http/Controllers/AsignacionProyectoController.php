<?php

namespace App\Http\Controllers;

use App\Models\AsignacionProyecto;
use App\Models\Carrera;
use App\Models\Programa;
use App\Models\Tutor;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;


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

        $asignacion = AsignacionProyecto::create([
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
    
        $asignacion->load(['usuario', 'tutor.usuario', 'estudiante.usuario', 'carrera', 'programa']);
        // 3) Preparar datos para n8n
        $payload = [
            'id_asignacion'   => $asignacion->id_asignacion,
            'titulo_proyecto' => $asignacion->titulo_proyecto,
            'estado'          => $asignacion->estado,
            'fecha_asignacion'=> optional($asignacion->fecha_asignacion)->toDateString(),

            'carrera'  => optional($asignacion->carrera)->nombre,
            'programa' => optional($asignacion->programa)->nombre,

            'estudiante' => [
                'id'     => optional($asignacion->estudiante)->id_estudiante,
                'nombre' => optional(optional($asignacion->estudiante)->usuario)->name ?? null,
                'email'  => optional(optional($asignacion->estudiante)->usuario)->email ?? null,
            ],
            'tutor' => [
                'id'     => optional($asignacion->tutor)->id_tutor,
                'nombre' => optional(optional($asignacion->tutor)->usuario)->name ?? null,
                'email'  => optional(optional($asignacion->tutor)->usuario)->email ?? null,
            ],
            'usuario_asigna' => [
                'id'     => optional($asignacion->usuario)->id,
                'nombre' => optional($asignacion->usuario)->name,
                'email'  => optional($asignacion->usuario)->email,
            ],
            'observacion'    => $asignacion->observacion,
            'link_plataforma'=> route('asignaciones.index'), // o una ruta show si la tienes
        ];
        // 4) URL del Webhook de n8n (Production URL)
    $n8nUrl = env('N8N_WEBHOOK_ASIGNACION', 'http://localhost:5678/webhook/asignacion-creada');
    try {
        Http::post($n8nUrl, $payload);
    } catch (\Throwable $e) {
        // No romper la app si falla n8n
        \Log::warning('Error enviando asignación a n8n: '.$e->getMessage());
    }

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
    $asignacion->load(['usuario', 'tutor.usuario', 'estudiante.usuario', 'carrera', 'programa']);

        $payload = [
            'id_asignacion'   => $asignacion->id_asignacion,
            'titulo_proyecto' => $asignacion->titulo_proyecto,
            'estado'          => 'Eliminado',
            'fecha_asignacion'=> optional($asignacion->fecha_asignacion)->toDateString(),
            'fecha_eliminacion' => now()->toDateTimeString(),

            'carrera'  => optional($asignacion->carrera)->nombre,
            'programa' => optional($asignacion->programa)->nombre,

            'estudiante' => [
                'id'     => optional($asignacion->estudiante)->id_estudiante,
                'nombre' => optional(optional($asignacion->estudiante)->usuario)->name,
                'email'  => optional(optional($asignacion->estudiante)->usuario)->email,
            ],
            'tutor' => [
                'id'     => optional($asignacion->tutor)->id_tutor,
                'nombre' => optional(optional($asignacion->tutor)->usuario)->name,
                'email'  => optional(optional($asignacion->tutor)->usuario)->email,
            ],
            'usuario_asigna' => [
                'id'     => optional($asignacion->usuario)->id,
                'nombre' => optional($asignacion->usuario)->name,
                'email'  => optional($asignacion->usuario)->email,
            ],
            'observacion'     => $asignacion->observacion,
            'link_plataforma' => route('asignaciones.index'),

            'motivo'          => 'Asignación eliminada desde el sistema',
        ];

        $n8nUrl = env('N8N_WEBHOOK_ASIGNACION_ELIMINADA', 'http://localhost:5678/webhook/asignacion-eliminada');

        try {
            Http::post($n8nUrl, $payload);
        } catch (\Throwable $e) {
            \Log::error('Error enviando asignación eliminada a n8n: '.$e->getMessage());
        }

        $asignacion->delete();
        return back()->with('success','Asignación eliminada.');
    }
    
}
