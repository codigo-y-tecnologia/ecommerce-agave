<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckMaintenanceMode
{

    public function handle(Request $request, Closure $next)
    {
        if (config_sistema('modo_mantenimiento') == 1) {

            if (!Auth::check()) {
                return response()->view('maintenance');
            }

            if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
                return response()->view('maintenance');
            }
        }

        return $next($request);
    }
}
