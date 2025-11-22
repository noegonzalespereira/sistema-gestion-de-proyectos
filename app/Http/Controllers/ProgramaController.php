<?php

namespace App\Http\Controllers;

use App\Models\Programa;
use Illuminate\Http\Request;

class ProgramaController extends Controller
{
    public function index()
    {
        $programas = Programa::orderBy('created_at', 'desc')->get();
        return view('admin.programas.index', compact('programas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => ['required','string','max:150','unique:programas,nombre'],
            'descripcion' => ['nullable','string','max:1000'],
        ], [
            'nombre.unique'   => 'Ya existe un programa con ese nombre.',
            'nombre.required' => 'El nombre del programa es obligatorio.',
        ]);

        Programa::create([
            'nombre'      => trim($request->nombre),
            'descripcion' => $request->descripcion,
        ]);

        return back()->with('success', 'Programa creado correctamente.');
    }

    public function edit(Programa $programa)
    {
        return view('admin.programas.edit', compact('programa'));
    }

    public function update(Request $request, Programa $programa)
    {
        $request->validate([
            'nombre'      => ['required','string','max:150','unique:programas,nombre,'.$programa->id_programa.',id_programa'],
            'descripcion' => ['nullable','string','max:1000'],
        ]);

        $programa->update([
            'nombre'      => trim($request->nombre),
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route('programas.index')->with('success', 'Programa actualizado correctamente.');
    }

    public function destroy(Programa $programa)
    {
        // (si más adelante hay FK desde proyectos, considera try/catch para constraint)
        $programa->delete();
        return back()->with('success', 'Programa eliminado.');
    }
}
