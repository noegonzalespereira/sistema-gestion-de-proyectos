<?php
// app/Http/Controllers/CarreraController.php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Institucion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

class CarreraController extends Controller
{
    public function index()
    {
        $carreras     = Carrera::with('institucion')->orderBy('created_at','desc')->get();
        $instituciones = Institucion::orderBy('nombre')->get();

        return view('admin.carreras.index', compact('carreras','instituciones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'         => ['required','string','max:150'],
            'sigla'          => ['nullable','string','max:20'],
            'id_institucion' => ['required','exists:institucion,id_institucion'],
        ]);

        Carrera::create($request->only('nombre','sigla','id_institucion'));

        return back()->with('success','Carrera creada correctamente.');
    }

    public function edit(Carrera $carrera)
    {
        $instituciones = Institucion::orderBy('nombre')->get();
        return view('admin.carreras.edit', compact('carrera','instituciones'));
    }

    public function update(Request $request, Carrera $carrera)
    {
        $request->validate([
            'nombre' => [
                'required','string','max:150',
                // (opcional) si quieres evitar duplicados por institución:
                Rule::unique('carreras','nombre')
                    ->ignore($carrera->id_carrera,'id_carrera')
                    ->where(fn($q)=>$q->where('id_institucion', $request->id_institucion)),
            ],
            'sigla'          => ['nullable','string','max:20'],
            'id_institucion' => ['required','exists:institucion,id_institucion'],
        ]);

        $carrera->update($request->only('nombre','sigla','id_institucion'));

        return redirect()->route('carreras.index')->with('success','Carrera actualizada correctamente.');
    }

    public function destroy(Carrera $carrera)
    {
        try {
            $carrera->delete();
            return back()->with('success','Carrera eliminada.');
        } catch (QueryException $e) {
            // error FK (p.ej. tiene estudiantes/proyectos)
            return back()->with('error','No se puede eliminar: la carrera tiene relaciones asociadas.');
        }
    }
}
