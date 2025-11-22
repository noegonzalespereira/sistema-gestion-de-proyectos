<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyecto;
use App\Models\Carrera;
use App\Models\Programa;
use App\Models\Tutor;

class RepositorioController extends Controller
{
    public function index()
    {
        $proyectos = Proyecto::with(['estudiante.usuario', 'tutor.usuario', 'programa', 'carrera'])
            ->orderBy('fecha_defensa', 'desc')
            ->get();
        $carreras = Carrera::all();
        $programas = Programa::all();
        $tutores = Tutor::with('usuario')->get();

        return view('repositorio.index', compact('proyectos','carreras', 'programas', 'tutores'));
    }

    public function buscar(Request $request)
    {
        $query = Proyecto::with(['estudiante.usuario', 'tutor.usuario', 'programa', 'carrera']);

        if ($request->filled('texto')) {
            $query->where(function ($q) use ($request) {
                $q->where('titulo', 'like', "%{$request->texto}%")
                  ->orWhere('resumen', 'like', "%{$request->texto}%");
            });
        }

        if ($request->filled('id_programa')) {
            $query->where('id_programa', $request->id_programa);
        }

        if ($request->filled('id_carrera')) {
            $query->where('id_carrera', $request->id_carrera);
        }

        if ($request->filled('anio')) {
            $query->where('anio', $request->anio);
        }

        if ($request->filled('id_tutor')) {
            $query->where('id_tutor', $request->id_tutor);
        }

        if ($request->filled('calificacion_min')) {
            $query->where('calificacion', '>=', $request->calificacion_min);
        }

        if ($request->filled('calificacion_max')) {
            $query->where('calificacion', '<=', $request->calificacion_max);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_defensa', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_defensa', '<=', $request->fecha_hasta);
        }

        $proyectos = $query->orderBy('fecha_defensa', 'desc')->get();
        $carreras = Carrera::all();
        $programas = Programa::all();
        $tutores = Tutor::with('usuario')->get();

        return view('repositorio.index', compact('proyectos','carreras', 'programas', 'tutores'));
    }
}
