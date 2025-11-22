<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use App\Models\User;
use App\Models\Carrera;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EstudianteAdminController extends Controller
{
    public function index()
    {
        $estudiantes = Estudiante::with(['usuario', 'carrera'])
            ->latest()
            ->get();

        // Usuarios con rol Estudiante que aún no tienen fila en 'estudiantes'
        $usuariosDisponibles = User::where('rol', 'Estudiante')
            ->whereDoesntHave('estudiante') // requiere relación en User
            ->orderBy('name')
            ->get();

        $carreras = Carrera::orderBy('nombre')->get();

        return view('admin.estudiantes.index', compact('estudiantes', 'usuariosDisponibles', 'carreras'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_usuario' => [
                'required',
                Rule::exists('users', 'id')->where(fn($q) => $q->where('rol', 'Estudiante')),
                Rule::unique('estudiantes', 'id_usuario'),
            ],
            'ci' => ['required', 'string', 'max:30', 'unique:estudiantes,ci'],
            'id_carrera' => ['nullable', Rule::exists('carreras', 'id_carrera')],
        ], [
            'id_usuario.required' => 'Selecciona un usuario.',
            'id_usuario.exists'   => 'El usuario seleccionado no tiene rol Estudiante.',
            'id_usuario.unique'   => 'Este usuario ya está registrado como Estudiante.',
            'ci.required'         => 'El CI es obligatorio.',
            'ci.unique'           => 'El CI ya está registrado.',
        ]);

        Estudiante::create([
            'id_usuario' => $request->id_usuario,
            'ci'         => $request->ci,
            'id_carrera' => $request->id_carrera,
        ]);

        return back()->with('success', 'Estudiante creado correctamente.');
    }

    public function edit(Estudiante $estudiante)
    {
        // Permite mantener el usuario actual o elegir otro estudiante libre
        $usuariosDisponibles = User::where('rol', 'Estudiante')
            ->where(function ($q) use ($estudiante) {
                $q->whereDoesntHave('estudiante')
                  ->orWhere('id', $estudiante->id_usuario);
            })
            ->orderBy('name')
            ->get();

        $carreras = Carrera::orderBy('nombre')->get();

        return view('admin.estudiantes.edit', compact('estudiante', 'usuariosDisponibles', 'carreras'));
    }

    public function update(Request $request, Estudiante $estudiante)
    {
        $request->validate([
            'id_usuario' => [
                'required',
                Rule::exists('users', 'id')->where(fn($q) => $q->where('rol', 'Estudiante')),
                Rule::unique('estudiantes', 'id_usuario')->ignore($estudiante->id_estudiante, 'id_estudiante'),
            ],
            'ci' => [
                'required', 'string', 'max:30',
                Rule::unique('estudiantes', 'ci')->ignore($estudiante->id_estudiante, 'id_estudiante'),
            ],
            'id_carrera' => ['nullable', Rule::exists('carreras', 'id_carrera')],
        ]);

        $estudiante->update([
            'id_usuario' => $request->id_usuario,
            'ci'         => $request->ci,
            'id_carrera' => $request->id_carrera,
        ]);

        return redirect()->route('estudiantes.index')->with('success', 'Estudiante actualizado correctamente.');
    }

    public function destroy(Estudiante $estudiante)
    {
        $estudiante->delete();
        return back()->with('success', 'Estudiante eliminado.');
    }
}
