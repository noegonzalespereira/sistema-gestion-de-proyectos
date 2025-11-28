<?php
namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Modulo;
use App\Models\ModuloMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\AsignacionProyecto;
use Carbon\Carbon;

class ModuloController extends Controller
{
    public function store(Request $request, $asignacionId) {
        $data = $request->validate([
            'titulo' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'fecha_limite' => 'nullable|date',
        ]);
        $data['id_asignacion'] = $asignacionId;
        $modulo=Modulo::create($data);
        try {
            // cargamos la asignación con estudiante y tutor
            $asignacion = $modulo->asignacion()
                ->with(['estudiante.usuario', 'tutor.usuario'])
                ->first();

            $payload = [
                'evento' => 'modulo_creado',
                'modulo' => [
                    'id_modulo'    => $modulo->id_modulo,
                    'titulo'       => $modulo->titulo,
                    'descripcion'  => $modulo->descripcion,
                    'fecha_limite' => $modulo && $modulo->fecha_limite
                        ? Carbon::parse($modulo->fecha_limite)->toDateString()
                        : null,
                ],
                'asignacion' => [
                    'id_asignacion'   => $asignacion->id_asignacion ?? null,
                    'titulo_proyecto' => $asignacion->titulo_proyecto ?? null,
                ],
                'estudiante' => [
                    'id'     => optional($asignacion->estudiante)->id_estudiante ?? null,
                    'nombre' => optional(optional($asignacion->estudiante)->usuario)->name ?? null,
                    'email'  => optional(optional($asignacion->estudiante)->usuario)->email ?? null,
                ],
                'tutor' => [
                    'id'     => optional($asignacion->tutor)->id_tutor ?? null,
                    'nombre' => optional(optional($asignacion->tutor)->usuario)->name ?? null,
                    'email'  => optional(optional($asignacion->tutor)->usuario)->email ?? null,
                ],
                'link_plataforma' => route('estudiante.proyecto'), // o ruta específica al módulo
            ];

            Http::post(env('N8N_WEBHOOK_MODULO_EVENTOS'), $payload);
        } catch (\Throwable $e) {
            \Log::warning('Error enviando modulo_creado a n8n: '.$e->getMessage());
        }

        return back()->with('success','Módulo creado.');
    }

    public function destroy(Modulo $modulo) {
        $modulo->delete();
        return back()->with('success','Módulo eliminado.');
    }

    public function storeMaterial(Request $request, Modulo $modulo) {
        $data = $request->validate([
            'tipo'   => 'required|in:pdf,enlace,video',
            'titulo' => 'nullable|string|max:200',
            'url'    => 'nullable|url',
            'archivo'=> 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar|max:5120',
        ]);
        $mat = new ModuloMaterial([
            'tipo'   => $data['tipo'],
            'titulo' => $data['titulo'] ?? null,
            'url'    => $data['url'] ?? null,
        ]);
        if ($request->hasFile('archivo')) {
            $mat->path = $request->file('archivo')->store('modulos','public');
        }
        $modulo->materiales()->save($mat);
        return back()->with('success','Material agregado.');
    }

    public function evaluar(Request $request, Modulo $modulo) {
        $data = $request->validate([
            'estado' => 'required|in:pendiente,observado,aprobado',
            'calificacion' => 'nullable|numeric|min:0|max:100',
            'fecha_limite' => 'nullable|date',
        ]);
        $modulo->update($data);
        return back()->with('success','Módulo evaluado.');
    }
}
