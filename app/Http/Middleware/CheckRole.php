<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string[]  ...$roles
     * 
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {

        if (!Auth::check()) {
            return redirect()->route('login'); // si no está logueado
        }

        $user = Auth::user();

        // Verifica si el rol del usuario está en la lista de roles permitidos
        if (!in_array($user->eRol, $roles)) {
            abort(403, 'Acceso denegado'); // Forbidden
        }

        return $next($request);
    }
}
