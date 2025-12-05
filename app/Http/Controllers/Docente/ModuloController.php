<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Modulo;
use App\Models\ModuloMaterial;
use App\Models\AsignacionProyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ModuloController extends Controller
{
    /* ============================================================
     *  CREAR MÓDULO BASE (se replica sin fecha a todos los alumnos)
     * ============================================================ */
    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'      => 'required|string|max:200',
            'descripcion' => 'nullable|string',
        ]);

        $tutor = \App\Models\Tutor::where('id_usuario', auth()->id())->firstOrFail();

        $asignaciones = AsignacionProyecto::where('id_tutor', $tutor->id_tutor)->get();

        foreach ($asignaciones as $asignacion) {
            Modulo::create([
                'id_asignacion' => $asignacion->id_asignacion,
                'titulo'        => $data['titulo'],
                'descripcion'   => $data['descripcion'],
                'fecha_limite'  => null,       // módulo base
                'estado'        => 'pendiente',
            ]);
        }

        return back()->with('success', 'Módulo base creado para todos tus estudiantes.');
    }


    /* ============================================================
     *  ELIMINAR MÓDULO (solo uno, NO global)
     * ============================================================ */
    public function destroy(Modulo $modulo)
    {
        $modulo->materiales()->delete();
        $modulo->delete();

        return back()->with('success', 'Módulo eliminado.');
    }


    /* ============================================================
     *  AGREGAR MATERIAL A UN MÓDULO (replica si es módulo base)
     * ============================================================ */
    public function storeMaterial(Request $request, Modulo $modulo)
    {
        $data = $request->validate([
            'tipo'   => 'required|in:pdf,enlace,video',
            'titulo' => 'nullable|string|max:200',
            'url'    => 'nullable|url',
            'archivo'=> 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar|max:5120',
        ]);

        // Material original
        $material = new ModuloMaterial([
            'tipo'   => $data['tipo'],
            'titulo' => $data['titulo'] ?? null,
            'url'    => $data['url'] ?? null,
        ]);

        if ($request->hasFile('archivo')) {
            $material->path = $request->file('archivo')->store('modulos', 'public');
        }

        $modulo->materiales()->save($material);

        /* --------------------------------------------------------
         *  Si el módulo es BASE (sin fecha_limite) → replicar
         * -------------------------------------------------------- */
        if ($modulo->fecha_limite === null) {

            $asignacionModulo = $modulo->asignacion;
            $tutorId = $asignacionModulo?->id_tutor;

            if ($tutorId) {
                $idsAsignacionesTutor = AsignacionProyecto::where('id_tutor', $tutorId)
                    ->pluck('id_asignacion');

                $modulosClones = Modulo::whereIn('id_asignacion', $idsAsignacionesTutor)
                    ->where('titulo', $modulo->titulo)
                    ->where('descripcion', $modulo->descripcion)
                    ->whereNull('fecha_limite')
                    ->where('id_modulo', '!=', $modulo->id_modulo)
                    ->get();

                foreach ($modulosClones as $clone) {
                    $clone->materiales()->create([
                        'tipo'   => $material->tipo,
                        'titulo' => $material->titulo,
                        'url'    => $material->url,
                        'path'   => $material->path,
                    ]);
                }
            }
        }

        return back()->with('success', 'Material agregado (y replicado en tus demás módulos base).');
    }


    /* ============================================================
     *  EVALUAR MÓDULO (y disparar n8n si se pone fecha por 1ª vez)
     * ============================================================ */
    public function evaluar(Request $request, Modulo $modulo)
    {
         // 1. Determinar si el módulo es BASE (pertenece al docente)
        $asignacion = $modulo->asignacion;
        $tutor = \App\Models\Tutor::where('id_usuario', auth()->id())->first();
        $isBase = $modulo->fecha_limite === null
               && $asignacion
               && $tutor
               && $asignacion->id_tutor == $tutor->id_tutor;

        // 2. Si es base → NO permitir evaluación
        if ($isBase) {
            return back()->with('error', 'Este es un módulo base (plantilla del tutor) y no puede ser evaluado.');
        }

        
        

        $data = $request->validate([
            'estado'       => 'nullable|string',
         
            'fecha_limite' => 'nullable|date',
        ]);

        $fechaAntes = $modulo->fecha_limite;
        $avances = $modulo->avances()->with('correcciones')->get();

        $notas = [];

        foreach ($avances as $av) {
        foreach ($av->correcciones as $cor) {
            if (!is_null($cor->nota)) {
                $notas[] = $cor->nota;
            }
        }
    }

        // Calcular promedio REAL
        $promedio = count($notas) > 0 ? round(array_sum($notas) / count($notas),2) : null;

        // Guardar la nota del módulo
        $modulo->calificacion = $promedio;
        $modulo->estado       = $data['estado'] ?? $modulo->estado;
        $modulo->fecha_limite = $data['fecha_limite'] ?? $modulo->fecha_limite;

        $modulo->save();

        


        // Notificar solo si ANTES no tenía fecha y AHORA sí
        if (!$fechaAntes && $modulo->fecha_limite) {
            try {
                $asignacion = $modulo->asignacion()->with(['estudiante.usuario', 'tutor.usuario'])->first();


                Http::post(env('N8N_WEBHOOK_MODULO_EVENTOS'), [
                    'evento' => 'modulo_creado',
                    'modulo' => [
                        'id_modulo'    => $modulo->id_modulo,
                        'titulo'       => $modulo->titulo,
                        'descripcion'  => $modulo->descripcion,
                        'fecha_limite' => $modulo->fecha_limite,
                    ],
                    'asignacion' => [
                        'titulo_proyecto' => $asignacion->titulo_proyecto,
                    ],
                    'estudiante' => [
                        'nombre' => optional($asignacion->estudiante->usuario)->name,
                        'email'  => optional($asignacion->estudiante->usuario)->email,
                    ],
                    'tutor' => [
                        'nombre' => optional($asignacion->tutor->usuario)->name,
                        'email'  => optional($asignacion->tutor->usuario)->email,
                    ],
                    'link_plataforma' => route('estudiante.proyecto', $asignacion->id_asignacion), // ← AGREGA ESTO

                ]);

            } catch (\Throwable $e) {
                \Log::warning('Error enviando evento modulo_creado a n8n: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Módulo actualizado.');
    }


    /* ============================================================
     *  EDITAR MÓDULO INDIVIDUAL
     * ============================================================ */
    public function update(Request $request, Modulo $modulo)
    {
        $data = $request->validate([
            'titulo'      => 'required|string|max:200',
            'descripcion' => 'nullable|string',
        ]);

        $modulo->update($data);

        return back()->with('success', 'Módulo actualizado correctamente.');
    }


    /* ============================================================
     *  EDITAR MATERIAL INDIVIDUAL
     * ============================================================ */
    public function updateMaterial(Request $request, ModuloMaterial $material)
    {
        $data = $request->validate([
            'tipo'   => 'required|in:pdf,enlace,video',
            'titulo' => 'nullable|string|max:200',
            'url'    => 'nullable|url',
            'archivo'=> 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar|max:5120',
        ]);

        $material->tipo   = $data['tipo'];
        $material->titulo = $data['titulo'] ?? null;
        $material->url    = $data['url'] ?? null;

        if ($request->hasFile('archivo')) {
            $material->path = $request->file('archivo')->store('modulos', 'public');
        }

        $material->save();

        return back()->with('success', 'Material actualizado correctamente.');
    }


    /* ============================================================
     *  ELIMINAR MATERIAL INDIVIDUAL
     * ============================================================ */
    public function destroyMaterial(ModuloMaterial $material)
    {
        $material->delete();
        return back()->with('success', 'Material eliminado correctamente.');
    }


    /* ============================================================
     *  🔥 ELIMINAR MÓDULO BASE GLOBALMENTE
     *  (para todos los estudiantes del grupo)
     * ============================================================ */
    public function destroyBase($id_modulo)
    {
        $modulo = Modulo::findOrFail($id_modulo);

        $titulo      = $modulo->titulo;
        $descripcion = $modulo->descripcion;

        $modulosGrupo = Modulo::where('titulo', $titulo)
            ->where('descripcion', $descripcion)
            ->whereNull('fecha_limite')
            ->get();

        foreach ($modulosGrupo as $m) {
            $m->materiales()->delete();
            $m->delete();
        }

        return back()->with('success', 'Módulo base eliminado para todos los estudiantes.');
    }


    /* ============================================================
     *  🔥 EDITAR MÓDULO BASE GLOBALMENTE
     *  (actualiza título/descripcion en todos los clones base)
     * ============================================================ */
    public function updateBase(Request $request, $id_modulo)
    {
        $data = $request->validate([
            'titulo'      => 'required|string|max:200',
            'descripcion' => 'nullable|string',
        ]);

        $modulo = Modulo::findOrFail($id_modulo);

        $tituloOld      = $modulo->titulo;
        $descripcionOld = $modulo->descripcion;

        $modulosGrupo = Modulo::where('titulo', $tituloOld)
            ->where('descripcion', $descripcionOld)
            ->whereNull('fecha_limite')
            ->get();

        foreach ($modulosGrupo as $m) {
            $m->update($data);
        }

        return back()->with('success', 'Módulo base actualizado correctamente para todos los estudiantes.');
    }


    /* ============================================================
     *  🔥 AGREGAR MATERIAL GLOBAL (a TODOS los módulos base)
     *  Usa la ruta: docente.modulos.materiales.store.global
     * ============================================================ */
    public function storeMaterialGlobal(Request $request, $id_modulo)
    {
        $data = $request->validate([
            'tipo'   => 'required|in:pdf,enlace,video',
            'titulo' => 'nullable|string|max:200',
            'url'    => 'nullable|url',
            'archivo'=> 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar|max:5120',
        ]);

        $moduloBase = Modulo::findOrFail($id_modulo);

        $tituloModulo      = $moduloBase->titulo;
        $descripcionModulo = $moduloBase->descripcion;

        $modulosGrupo = Modulo::where('titulo', $tituloModulo)
            ->where('descripcion', $descripcionModulo)
            ->whereNull('fecha_limite')
            ->get();

        $path = null;
        if ($request->hasFile('archivo')) {
            $path = $request->file('archivo')->store('modulos', 'public');
        }

        foreach ($modulosGrupo as $m) {
            $m->materiales()->create([
                'tipo'   => $data['tipo'],
                'titulo' => $data['titulo'] ?? null,
                'url'    => $data['url'] ?? null,
                'path'   => $path,
            ]);
        }

        return back()->with('success', 'Material agregado en todos los módulos base de este tipo.');
    }


    /* ============================================================
     *  🔥 EDITAR MATERIAL GLOBAL
     *  (sincroniza el cambio en todos los módulos base del grupo)
     * ============================================================ */
    public function updateMaterialGlobal(Request $request, $id_material)
    {
        $material = ModuloMaterial::findOrFail($id_material);

        $data = $request->validate([
            'tipo'   => 'required|in:pdf,enlace,video',
            'titulo' => 'nullable|string|max:200',
            'url'    => 'nullable|url',
            'archivo'=> 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar|max:5120',
        ]);

        $modulo = $material->modulo;

        // Valores originales para localizar los "clones"
        $originalTipo   = $material->tipo;
        $originalTitulo = $material->titulo;
        $originalUrl    = $material->url;
        $originalPath   = $material->path;

        $path = $originalPath;
        if ($request->hasFile('archivo')) {
            $path = $request->file('archivo')->store('modulos', 'public');
        }

        $modulosGrupo = Modulo::where('titulo', $modulo->titulo)
            ->where('descripcion', $modulo->descripcion)
            ->whereNull('fecha_limite')
            ->get();

        foreach ($modulosGrupo as $m) {
            $query = $m->materiales()->where('tipo', $originalTipo);

            // match por título
            if ($originalTitulo === null) {
                $query->whereNull('titulo');
            } else {
                $query->where('titulo', $originalTitulo);
            }

            // match por url
            if ($originalUrl === null) {
                $query->whereNull('url');
            } else {
                $query->where('url', $originalUrl);
            }

            // match por path
            if ($originalPath === null) {
                $query->whereNull('path');
            } else {
                $query->where('path', $originalPath);
            }

            $query->update([
                'tipo'   => $data['tipo'],
                'titulo' => $data['titulo'] ?? null,
                'url'    => $data['url'] ?? null,
                'path'   => $path,
            ]);
        }

        return back()->with('success', 'Material actualizado globalmente en todos los módulos base.');
    }


    /* ============================================================
     *  🔥 ELIMINAR MATERIAL GLOBAL
     *  (borra ese material en todos los módulos base del grupo)
     * ============================================================ */
    public function destroyMaterialGlobal($id_material)
    {
        $material = ModuloMaterial::findOrFail($id_material);
        $modulo   = $material->modulo;

        $originalTipo   = $material->tipo;
        $originalTitulo = $material->titulo;
        $originalUrl    = $material->url;
        $originalPath   = $material->path;

        $modulosGrupo = Modulo::where('titulo', $modulo->titulo)
            ->where('descripcion', $modulo->descripcion)
            ->whereNull('fecha_limite')
            ->get();

        foreach ($modulosGrupo as $m) {
            $query = $m->materiales()->where('tipo', $originalTipo);

            if ($originalTitulo === null) {
                $query->whereNull('titulo');
            } else {
                $query->where('titulo', $originalTitulo);
            }

            if ($originalUrl === null) {
                $query->whereNull('url');
            } else {
                $query->where('url', $originalUrl);
            }

            if ($originalPath === null) {
                $query->whereNull('path');
            } else {
                $query->where('path', $originalPath);
            }

            $query->delete();
        }

        return back()->with('success', 'Material eliminado globalmente de todos los módulos base.');
    }
}
