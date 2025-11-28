<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FaltasModulo;

class FaltasController extends Controller
{
    public function index()
    {
        $faltas = FaltasModulo::with([
                'modulo',
                'asignacion.estudiante.usuario'
            ])
            ->orderBy('fecha_registro', 'desc')
            ->get();

        return view('docente.faltas.index', compact('faltas'));
    }

    public function rehabilitar(Request $request, $id)
    {
        // Validar el form
        $request->validate([
            'nueva_fecha_limite' => 'required|date'
        ]);

        // Encontrar la falta
        $falta = FaltasModulo::findOrFail($id);

        // Quitar bloqueo
        $falta->bloqueado = false;
        $falta->rehabilitado = true;
        $falta->nueva_fecha_limite = $request->nueva_fecha_limite;
        $falta->save();

        // Actualizar fecha límite del módulo
        $modulo = $falta->modulo;
        $modulo->fecha_limite = $request->nueva_fecha_limite;
        $modulo->save();

        return back()->with('success', 'Estudiante rehabilitado correctamente.');
    }
    public function porAsignacion($id)
    {
        $faltas = FaltasModulo::with(['modulo', 'asignacion.estudiante.usuario'])
            ->where('id_asignacion', $id)
            ->orderBy('fecha_registro', 'desc')
            ->get();

        return view('docente.faltas.index', compact('faltas'));
    }

}
