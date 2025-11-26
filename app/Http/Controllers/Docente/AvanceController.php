<?php 
namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Avance;
use App\Models\AsignacionProyecto;
use Illuminate\Http\Request;
use App\Models\Modulo;  
use Illuminate\Support\Facades\Http;  

class AvanceController extends Controller
{
    public function index(AsignacionProyecto $asignacion) {
        $avances = Avance::with('usuario')
            ->where('id_asignacion', $asignacion->id_asignacion)
            ->orderBy('id_modulo')
            ->orderBy('created_at')
            ->get();
        $avancesPorModulo = $avances->groupBy('id_modulo');

        // crea una vista simple o devuélvelos como quieras
        return view('docente.partials.avances', [
            'asignacion'       => $asignacion,
            'avancesPorModulo' => $avancesPorModulo,
        ]);
    }

    public function store(Request $request, AsignacionProyecto $asignacion) {
        $data = $request->validate([
            'id_modulo'   => 'required|exists:modulos,id_modulo',
            'titulo' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'archivo' => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:5120',
        ]);
        $avance = new Avance([
            'id_asignacion' => $asignacion->id_asignacion,
            'id_usuario'    => auth()->id(),
            'titulo'        => $data['titulo'],
            'descripcion'   => $data['descripcion'] ?? null,
        ]);
        if ($request->hasFile('archivo')) {
            $avance->path = $request->file('archivo')->store('avances','public');
        }
        $avance->save();
        try {
            $modulo = Modulo::with([
                    'asignacion.estudiante.usuario',
                    'asignacion.tutor.usuario',
                ])->find($data['id_modulo']);

            $asig = $modulo->asignacion;

            $payload = [
                'event_type' => 'avance_subido',
                'modulo' => [
                    'id_modulo'    => $modulo->id_modulo,
                    'titulo'       => $modulo->titulo,
                    'fecha_limite' => optional($modulo->fecha_limite)->toDateString(),
                    'numero'       => $modulo->numero ?? null,
                ],
                'asignacion' => [
                    'id_asignacion'   => $asig->id_asignacion ?? null,
                    'titulo_proyecto' => $asig->titulo_proyecto ?? null,
                ],
                'avance' => [
                    'id_avance'   => $avance->id_avance,
                    'titulo'      => $avance->titulo,
                    'fecha_envio' => $avance->created_at->toDateTimeString(),
                ],
                'estudiante' => [
                    'id'     => optional($asig->estudiante)->id_estudiante ?? null,
                    'nombre' => optional(optional($asig->estudiante)->usuario)->name ?? null,
                    'email'  => optional(optional($asig->estudiante)->usuario)->email ?? null,
                ],
                'tutor' => [
                    'id'     => optional($asig->tutor)->id_tutor ?? null,
                    'nombre' => optional(optional($asig->tutor)->usuario)->name ?? null,
                    'email'  => optional(optional($asig->tutor)->usuario)->email ?? null,
                ],
                'link_plataforma' => route('docente.asignaciones'), // o vista del módulo/avance
            ];

            Http::post(env('N8N_WEBHOOK_MODULO_EVENTOS'), $payload);
        } catch (\Throwable $e) {
            \Log::warning('Error enviando avance_subido a n8n: '.$e->getMessage());
        }
        return back()->with('success','Avance registrado.');
    }
}
