<?php 
namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\AsignacionProyecto;
use App\Models\Correccion;
use App\Models\Tutor;
use App\Models\Avance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class CorreccionController extends Controller
{
    // public function store(Request $request, AsignacionProyecto $asignacion) {
    //     $request->validate([
    //         'comentario'   => 'nullable|string',
    //         'fecha_limite' => 'nullable|date',
    //         'archivo'      => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:5120',
    //         'id_modulo'    => 'nullable|integer|exists:modulos,id_modulo', 
    //     ]);

    //     $tutor = Tutor::where('id_usuario', auth()->id())->firstOrFail();

    //     $corr = new Correccion([
    //         'id_asignacion' => $asignacion->id_asignacion,
    //         'id_modulo'     => $request->id_modulo,
    //         'id_tutor'      => $tutor->id_tutor,
    //         'comentario'    => $request->comentario,
    //         'fecha_limite'  => $request->fecha_limite,
    //     ]);

    //     if ($request->hasFile('archivo')) {
    //         $corr->path = $request->file('archivo')->store('correcciones','public');
    //     }
    //     $corr->save();

    //     return redirect()
    //         ->route('docente.asignaciones')
    //         ->with('success','Corrección enviada al estudiante.');
    // }

    public function storeForAvance(Request $request, Avance $avance)
    {
        $request->validate([
            'comentario'   => 'nullable|string',
            'nota'         => 'nullable|numeric|min:0|max:100',
            'fecha_limite' => 'nullable|date',
            'archivo'      => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:5120',
        ]);

        $tutor = Tutor::where('id_usuario', auth()->id())->firstOrFail();

        // Validar que este tutor es el tutor de la asignación
        if ($avance->asignacion->id_tutor !== $tutor->id_tutor) {
            abort(403);
        }

        $corr = new Correccion([
            'id_asignacion' => $avance->id_asignacion,
            'id_modulo'     => $avance->id_modulo,
            'id_avance'     => $avance->id_avance,
            'id_tutor'      => $tutor->id_tutor,
            'comentario'    => $request->comentario,
            'nota'          => $request->nota,
            'fecha_limite'  => $request->fecha_limite,
            // 'estado_modulo' => $request->estado_modulo ?? null,
        ]);

        if ($request->hasFile('archivo')) {
            $corr->path = $request->file('archivo')->store('correcciones', 'public');
        }
        $corr->save();
        // ============================================================
// 🔥 RECALCULAR NOTA DEL MÓDULO AUTOMÁTICAMENTE
// ============================================================
$modulo = $avance->modulo;

if ($modulo) {
    $todasLasCorrecciones = $modulo->avances()
        ->with('correcciones')
        ->get()
        ->flatMap(function ($av) {
            return $av->correcciones;
        });

    $notas = $todasLasCorrecciones
        ->whereNotNull('nota')
        ->pluck('nota')
        ->toArray();

    $promedio = count($notas) > 0
        ? round(array_sum($notas) / count($notas), 2)
        : null;

    $modulo->calificacion = $promedio;
    $modulo->save();
}


        if ($request->filled('fecha_limite') && $avance->modulo) {
            $avance->modulo->fecha_limite = $request->fecha_limite;
            $avance->modulo->save();
        }
        try {
            $avance->load([
                'asignacion.estudiante.usuario',
                'asignacion.tutor.usuario',
                'modulo',
            ]);

            $modulo = $avance->modulo;
            $asig   = $avance->asignacion;

            $payload = [
                'evento' => 'correccion_registrada',

                'modulo' => [
                    'id_modulo'    => $modulo?->id_modulo,
                    'titulo'       => $modulo?->titulo,
                    'descripcion'  => $modulo?->descripcion,
                    'fecha_limite' => $modulo && $modulo->fecha_limite
                        ? Carbon::parse($modulo->fecha_limite)->toDateString()
                        : null,
                ],

                'asignacion' => [
                    'id_asignacion' => $asig?->id_asignacion,
                    'titulo_proyecto'        => $asig?->titulo_proyecto,
                ],

                'correccion' => [
                    'id_correccion' => $corr->id_correccion,
                    'comentario'    => $corr->comentario,
                    'nota'          => $corr->nota,
                    'fecha_limite'  => $corr->fecha_limite,
                ],

                'estudiante' => [
                    'id'     => $asig->estudiante->id_estudiante ?? null,
                    'nombre' => $asig->estudiante->usuario->name ?? null,
                    'email'  => $asig->estudiante->usuario->email ?? null,
                ],

                'tutor' => [
                    'id'     => $asig->tutor->id_tutor ?? null,
                    'nombre' => $asig->tutor->usuario->name ?? null,
                    'email'  => $asig->tutor->usuario->email ?? null,
                ],

                'link_plataforma' => route('estudiante.proyecto'),
            ];

            Http::post(env('N8N_WEBHOOK_MODULO_EVENTOS'), $payload);
        } catch (\Throwable $e) {
            \Log::warning('Error enviando correccion_registrada a n8n: '.$e->getMessage());
        }

        return redirect()
            ->route('docente.asignaciones.show', $avance->id_asignacion)
            ->with('success', 'Corrección registrada para este avance.');
    }
}
