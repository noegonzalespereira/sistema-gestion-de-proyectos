<?php

namespace App\Http\Controllers;

use App\Models\Tribunal;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TribunalController extends Controller
{
    public function index()
    {
        $tribunales = Tribunal::orderBy('created_at', 'desc')->get();
        return view('admin.tribunales.index', compact('tribunales'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => ['required','string','max:150'],
            'email'  => ['nullable','email','max:150','unique:tribunales,email'],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'email.unique'    => 'Ya existe un tribunal con ese correo.',
        ]);

        Tribunal::create([
            'nombre' => trim($request->nombre),
            'email'  => $request->email,
        ]);

        return back()->with('success','Tribunal creado correctamente.');
    }

    public function edit(Tribunal $tribunal)
    {
        return view('admin.tribunales.edit', compact('tribunal'));
    }

    public function update(Request $request, Tribunal $tribunal)
    {
        $request->validate([
            'nombre' => ['required','string','max:150'],
            'email'  => [
                'nullable','email','max:150',
                Rule::unique('tribunales','email')->ignore($tribunal->id_tribunal, 'id_tribunal'),
            ],
        ]);

        $tribunal->update([
            'nombre' => trim($request->nombre),
            'email'  => $request->email,
        ]);

        return redirect()->route('tribunales.index')->with('success','Tribunal actualizado correctamente.');
    }

    public function destroy(Tribunal $tribunal)
    {
        // Si más adelante hay FK desde proyectos, considera manejar excepciones por constraints
        $tribunal->delete();
        return back()->with('success','Tribunal eliminado.');
    }
}
