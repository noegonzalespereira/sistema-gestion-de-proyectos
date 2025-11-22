<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::orderBy('created_at', 'desc')->get();
        return view('admin.usuarios.index', compact('usuarios')); 
    }

    public function registro(Request $request)
    {
        $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','email','max:255','unique:users,email'],
            'rol'      => ['required', Rule::in(['Administrador','Docente','Estudiante'])],
            'password' => ['required','min:8','confirmed'],
            'activo'   => ['nullable','boolean'],
        ]);

        User::create([
            'name'   => $request->name,
            'email'  => $request->email,
            'rol'    => $request->rol,
            'activo' => $request->boolean('activo', true),
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('usuarios.index')
        ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user)
    {
        return view('admin.usuarios.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => ['required','string','max:255'],
            'email' => ['required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'rol'   => ['required', Rule::in(['Administrador','Docente','Estudiante'])],
            'password' => ['nullable','min:8','confirmed'],
            'activo'   => ['nullable','boolean'],
        ]);

        $user->name   = $request->name;
        $user->email  = $request->email;
        $user->rol    = $request->rol;
        $user->activo = $request->boolean('activo');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('usuarios.edit', $user)->with('success','Usuario actualizado correctamente.');
    }

    public function toggle(User $user)
    {
        $user->activo = !$user->activo;
        $user->save();

        return back()->with('success', 'Estado actualizado.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'Usuario eliminado.');
    }
}
