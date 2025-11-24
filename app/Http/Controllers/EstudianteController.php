<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AsignacionProyecto;
use App\Models\Estudiante;
use App\Models\Modulo;
use App\Models\Avance;

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

    // Subir avance a un módulo concreto
    public function subirAvance(Request $request, Modulo $modulo)
    {
        $usuario = auth()->user();
        $est = Estudiante::where('id_usuario', $usuario->id)->firstOrFail();
        $asig = $modulo->asignacion;

        // Validar que el módulo pertenece a la asignación del estudiante
        abort_unless($asig && $asig->id_estudiante === $est->id_estudiante, 403);

        if ($modulo->estado === 'aprobado') {
            abort(403, 'Este módulo ya está aprobado, no puedes subir más avances.');
        }
        $modAnteriorPendiente = $asig->modulos()
            ->where('id_modulo', '<', $modulo->id_modulo)
            ->where('estado', '!=', 'aprobado')
            ->exists();

        if ($modAnteriorPendiente) {
            // 403 para que no puedan saltarse el flujo desde Postman etc.
            abort(403, 'Debes tener aprobado el módulo anterior antes de subir avances a este módulo.');
        }

        // Validar datos del avance
        $data = $request->validate([
            'titulo'      => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'archivo'     => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:5120',
        ]);

        // Crear avance ligado a la asignación y al módulo
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

        return back()->with('success', 'Tu avance fue enviado correctamente.');
    }

    // Actualizar un avance (si permites edición)
    public function actualizarAvance(Request $request, Avance $avance)
    {
        // solo el dueño puede editar
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
