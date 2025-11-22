<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\User;
use App\Models\Tutor;
use App\Models\Carrera;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Contadores globales
        $totalProyectos = Proyecto::count();
        $proyectosRevision = Proyecto::where('calificacion', null)->count(); // en revisión
        $proyectosAprobados = Proyecto::whereNotNull('calificacion')->count();
        $usuariosActivos = User::count();
        $tutores = Tutor::count();
        $carreras = Carrera::count();

        // Proyectos recientes
        $proyectosRecientes = Proyecto::with(['carrera', 'tutor.usuario'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalProyectos',
            'proyectosRevision',
            'proyectosAprobados',
            'usuariosActivos',
            'tutores',
            'carreras',
            'proyectosRecientes'
        ));
    }
}

