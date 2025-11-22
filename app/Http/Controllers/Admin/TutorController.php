<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TutorController extends Controller
{
    public function index()
    {
        // Lista de tutores con su usuario
        $tutores = Tutor::with('usuario')
            ->latest()
            ->get();

        // Docentes disponibles (rol Docente y que no estén ya en tutores)
        $docentesDisponibles = User::where('rol', 'Docente')
            ->whereDoesntHave('tutor')
            ->orderBy('name')
            ->get();

        return view('admin.tutores.index', compact('tutores', 'docentesDisponibles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_usuario' => [
                'required',
                Rule::exists('users', 'id')->where(fn($q) => $q->where('rol', 'Docente')),
                Rule::unique('tutores', 'id_usuario'),
            ],
            'item' => ['nullable', 'string', 'max:50'],
        ], [
            'id_usuario.required' => 'Selecciona un docente.',
            'id_usuario.exists'   => 'El usuario seleccionado no es un Docente válido.',
            'id_usuario.unique'   => 'Este docente ya está registrado como Tutor.',
        ]);

        Tutor::create([
            'id_usuario' => $request->id_usuario,
            'item'       => $request->item,
        ]);

        return back()->with('success', 'Tutor creado correctamente.');
    }

    public function edit(Tutor $tutor)
    {
        // Para editar, permitimos (opcional) reasignar a otro Docente libre
        $docentesDisponibles = User::where('rol', 'Docente')
            ->where(function ($q) use ($tutor) {
                $q->whereDoesntHave('tutor')
                  ->orWhere('id', $tutor->id_usuario);
            })
            ->orderBy('name')
            ->get();

        return view('admin.tutores.edit', compact('tutor', 'docentesDisponibles'));
    }

    public function update(Request $request, Tutor $tutor)
    {
        $request->validate([
            'id_usuario' => [
                'required',
                Rule::exists('users', 'id')->where(fn($q) => $q->where('rol', 'Docente')),
                Rule::unique('tutores', 'id_usuario')->ignore($tutor->id_tutor, 'id_tutor'),
            ],
            'item' => ['nullable', 'string', 'max:50'],
        ]);

        $tutor->update([
            'id_usuario' => $request->id_usuario,
            'item'       => $request->item,
        ]);

        return redirect()->route('tutores.index')->with('success', 'Tutor actualizado correctamente.');
    }

    public function destroy(Tutor $tutor)
    {
        $tutor->delete();
        return back()->with('success', 'Tutor eliminado.');
    }
}
