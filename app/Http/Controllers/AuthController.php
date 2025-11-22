<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // Vistas de login
    public function showAdminLogin()
    {
        return view('auth.login-admin');
    }

    public function showGeneralLogin()
    {
        return view('auth.login-general'); // para Docente/Estudiante
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required','email'],
            'password' => ['required','min:6'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($request->only('email','password'), $remember)) {
            $request->session()->regenerate();
            $user = auth()->user(); 

            // Redirección por rol
            return match ($user->rol) {
                // 'Administrador' => redirect()->route('proyectos.index'),
                'Administrador' => redirect()->route('repositorio.index'),

                'Docente'       => redirect()->route('docente.asignaciones'),
                'Estudiante'    => redirect()->route('estudiante.proyecto'),
                default         => redirect()->route('login.general'),
            };
        }

        return back()->withErrors(['email' => 'Credenciales inválidas'])->withInput();
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login.general');
    }
}

