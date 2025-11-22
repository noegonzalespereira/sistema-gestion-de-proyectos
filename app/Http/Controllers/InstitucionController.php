<?php

namespace App\Http\Controllers;

use App\Models\Institucion;
use Illuminate\Http\Request;

class InstitucionController extends Controller
{
    public function __construct()
    {
        // Si usas restricción de rol:
        $this->middleware(function ($request, $next) {
             if (!auth()->check() || auth()->user()->rol !== 'Administrador') {
                 abort(403, 'Acceso denegado: solo para administradores.');
             }
             return $next($request);
         });
    }

    public function index()
    {
        $instituciones = Institucion::orderBy('created_at', 'desc')->get();

        return view('admin.institucion.index', compact('instituciones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => ['required','string','max:150'],
            'sigla'       => ['nullable','string','max:20'],
            'descripcion' => ['nullable','string','max:1000'],
        ]);

        Institucion::create([
            'nombre'      => $request->nombre,
            'sigla'       => $request->sigla,
            'descripcion' => $request->descripcion,
        ]);

        return back()->with('success', 'Institución creada correctamente.');
    }

    public function edit(Institucion $institucion)
    {
        return view('admin.institucion.edit', compact('institucion'));
    }

    public function update(Request $request, Institucion $institucion)
    {
        $request->validate([
            'nombre'      => ['required','string','max:150'],
            'sigla'       => ['nullable','string','max:20'],
            'descripcion' => ['nullable','string','max:1000'],
        ]);

        $institucion->update([
            'nombre'      => $request->nombre,
            'sigla'       => $request->sigla,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route('institucion.edit', $institucion)
                         ->with('success', 'Institución actualizada correctamente.');
    }

    public function destroy(Institucion $institucion)
    {
        $institucion->delete();
        return back()->with('success', 'Institución eliminada.');
    }
}
