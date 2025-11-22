<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->rol, $roles)) {

            return redirect()->route('login.general')->withErrors([
                'auth'=>'No tienes permiso para acceder a esta sección.'
            ]);
        }

        return $next($request);
    }
}
