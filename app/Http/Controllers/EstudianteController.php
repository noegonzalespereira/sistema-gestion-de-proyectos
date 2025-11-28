<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AsignacionProyecto;
use App\Models\Estudiante;
use App\Models\Modulo;
use App\Models\Avance;
use Illuminate\Support\Facades\Http;
use App\Models\FaltasModulo;
use Carbon\Carbon;

class EstudianteController extends Controller
{
    // Pantalla principal "Mi Proyecto"
    public function proyecto()
    {
        $usuario = auth()->user();

        // Buscar estudiante ligado a este usuario
        $estudiante = Estudiante::where('id_usuario', $usuario->id)->first();

        if (!$estudiante) {
            return view('estudiante.proyecto')->with('asignacion', null);
        }

        // Buscamos la asignación (proyecto) actual del estudiante
        $asignacion = AsignacionProyecto::with([
                'programa',
                'carrera',
                'tutor.usuario',
                'modulos.materiales',
                'modulos.avances.correcciones.tutor.usuario',
                'correcciones.tutor.usuario',
            ])
            ->where('id_estudiante', $estudiante->id_estudiante)
            ->first();

        return view('estudiante.proyecto', compact('asignacion'));
    }



    // SUBIR AVANCE -------------------------------------------------------
    public function subirAvance(Request $request, Modulo $modulo)
    {
        $usuario = auth()->user();
        $est = Estudiante::where('id_usuario', $usuario->id)->firstOrFail();
        $asig = $modulo->asignacion;

        // VALIDAR QUE EL MÓDULO PERTENECE AL ESTUDIANTE
        abort_unless($asig && $asig->id_estudiante === $est->id_estudiante, 403);

        // NO PERMITIR SUBIR SI EL MÓDULO ESTÁ APROBADO
        if ($modulo->estado === 'aprobado') {
            abort(403, 'Este módulo ya está aprobado, no puedes subir más avances.');
        }

        // VALIDAR MÓDULO ANTERIOR APROBADO
        $modAnteriorPendiente = $asig->modulos()
            ->where('id_modulo', '<', $modulo->id_modulo)
            ->where('estado', '!=', 'aprobado')
            ->exists();

        if ($modAnteriorPendiente) {
            abort(403, 'Debes tener aprobado el módulo anterior antes de subir avances a este módulo.');
        }

        // --------------------------------------------------------------------
        // NUEVO: BLOQUEO POR FALTA
        // --------------------------------------------------------------------
        $falta = FaltasModulo::where('id_modulo', $modulo->id_modulo)
            ->where('id_estudiante', $est->id_estudiante)
            ->where('bloqueado', true)
            ->first();

        if ($falta) {
            abort(403, 'Este módulo está bloqueado porque no entregaste en la fecha límite. Debes contactar a tu docente.');
        }

        // --------------------------------------------------------------------
        // NUEVO: REGISTRAR FALTA SI YA PASÓ LA FECHA LÍMITE
        // --------------------------------------------------------------------
        if ($modulo->fecha_limite && Carbon::now()->gt(Carbon::parse($modulo->fecha_limite))) {

            FaltasModulo::firstOrCreate(
                [
                    'id_modulo'     => $modulo->id_modulo,
                    'id_asignacion' => $asig->id_asignacion,
                    'id_estudiante' => $est->id_estudiante,
                ],
                [
                    'fecha_limite_original' => $modulo->fecha_limite,
                    'motivo'                => 'No entregó avance antes de la fecha límite',
                    'bloqueado'             => true,
                ]
            );

            abort(403, 'Ya pasó la fecha límite. Este módulo ha sido bloqueado.');
        }

        // VALIDAR DATOS
        $data = $request->validate([
            'titulo'      => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'archivo'     => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:5120',
        ]);

        // GUARDAR AVANCE
        $avance = new Avance([
            'id_asignacion' => $asig->id_asignacion,
            'id_modulo'     => $modulo->id_modulo,
            'id_usuario'    => $usuario->id,
            'titulo'        => $data['titulo'],
            'descripcion'   => $data['descripcion'] ?? null,
        ]);

        if ($request->hasFile('archivo')) {
            $avance->path = $request->file('archivo')->store('avances', 'public');
        }

        $avance->save();

        // ENVIAR A N8N
        try {
            $asig->load(['estudiante.usuario', 'tutor.usuario']);

            $payload = [
                'evento' => 'avance_subido',
                'modulo' => [
                    'id_modulo'    => $modulo->id_modulo,
                    'titulo'       => $modulo->titulo,
                    'descripcion'  => $modulo->descripcion,
                    'fecha_limite' => optional($modulo->fecha_limite)->toDateString(),
                ],
                'asignacion' => [
                    'id_asignacion'   => $asig->id_asignacion,
                    'titulo_proyecto' => $asig->titulo_proyecto,
                ],
                'avance' => [
                    'id_avance'   => $avance->id_avance,
                    'titulo'      => $avance->titulo,
                    'descripcion' => $avance->descripcion,
                    'fecha_envio' => $avance->created_at->toDateTimeString(),
                ],
                'estudiante' => [
                    'id'     => $est->id_estudiante,
                    'nombre' => optional($asig->estudiante->usuario)->name,
                    'email'  => optional($asig->estudiante->usuario)->email,
                ],
                'tutor' => [
                    'id'     => $asig->tutor->id_tutor ?? null,
                    'nombre' => optional($asig->tutor->usuario)->name ?? null,
                    'email'  => optional($asig->tutor->usuario)->email ?? null,
                ],
                'link_plataforma' => route('docente.asignaciones'),
            ];

            Http::post(env('N8N_WEBHOOK_MODULO_EVENTOS'), $payload);

        } catch (\Throwable $e) {
            \Log::warning('Error enviando avance_subido a n8n: '.$e->getMessage());
        }

        return back()->with('success', 'Tu avance fue enviado correctamente.');
    }



    // Actualizar un avance
    public function actualizarAvance(Request $request, Avance $avance)
    {
        abort_unless($avance->id_usuario === auth()->id(), 403);

        $data = $request->validate([
            'titulo'      => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'archivo'     => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:5120',
        ]);

        $avance->titulo      = $data['titulo'];
        $avance->descripcion = $data['descripcion'] ?? null;

        if ($request->hasFile('archivo')) {
            $avance->path = $request->file('archivo')->store('avances', 'public');
        }

        $avance->save();

        return back()->with('success', 'Avance actualizado correctamente.');
    }



    // Eliminar avance
    public function eliminarAvance(Avance $avance)
    {
        abort_unless($avance->id_usuario === auth()->id(), 403);

        $avance->delete();

        return back()->with('success', 'Avance eliminado.');
    }
}
