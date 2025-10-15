<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


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

        Log::info('CheckRole middleware ejecutándose', ['roles' => $roles]);

        $user = Auth::user();

        if (!$user) {
            // No autenticado
            return redirect('/login')->withErrors([
                'vEmail' => 'Debes iniciar sesión para acceder a esta sección.'
            ]);
        }

        // Verifica si el rol del usuario está en la lista de roles permitidos
        if (!in_array($user->eRol, $roles)) {
            abort(403, 'Acceso denegado'); // Forbidden
        }

        return $next($request);
    }
}
