<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class SanitizeInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    /**
     * Maneja la solicitud entrante y limpia todos los datos del Request.
     */

    public function handle(Request $request, Closure $next)
    {
        
        $input = $request->all();
        $sqlKeywords = [
            'SELECT', 'INSERT', 'DELETE', 'UPDATE', 'DROP', 'CREATE', 'ALTER',
            'TRUNCATE', 'FROM', 'WHERE', 'AND', 'OR', 'JOIN', 'UNION', 'LIKE',
            'HAVING', 'EXEC', 'EXECUTE', 'GRANT', 'REVOKE', 'CAST', 'DECLARE',
            'REPLACE', 'RENAME', 'BENCHMARK', 'LOAD_FILE', 'INTO OUTFILE',
            'SHOW', 'DESCRIBE', 'EXPLAIN', 'MERGE', 'WITH', 'DATABASE',
            'TABLE', 'COLUMN', 'VIEW', 'INDEX', 'KEY', 'CONSTRAINT', 'SLEEP',
            'USER', 'VERSION', 'CURRENT_USER', 'SESSION_USER', 'SYSTEM_USER',
            'PASSWORD', 'DROP USER', 'DROP ROLE', 'LOAD XML', 'INTO DUMPFILE',
            'INFILE', 'DUMP', 'PRIVILEGES', 'SUPER', 'IDENTIFIED'
        ];

        $sanitized = [];
        $peligroso = false;

        foreach ($input as $key => $value) {
            if (is_string($value)) {
                // Limpieza básica
                $limpio = trim(strip_tags($value));

                // Buscar coincidencias SQL
                foreach ($sqlKeywords as $keyword) {
                    if (stripos($limpio, $keyword) !== false) {
                        Log::warning("⚠️ SQL injection detectada en campo '$key': $value");
                        $peligroso = true;
                        break;
                    }
                }

                $sanitized[$key] = $limpio;
            } else {
                $sanitized[$key] = $value;
            }
        }

        // Si detecta algo peligroso → devolver error y detener ejecución
        if ($peligroso) {
            return response()->json([
                'status' => 'error',
                'message' => 'Solicitud rechazada por contener posibles intentos de inyección SQL.'
            ], 400);
        }

        // Reemplazar los datos saneados en el request
        $request->merge($sanitized);

        return $next($request);
    
    }

}
