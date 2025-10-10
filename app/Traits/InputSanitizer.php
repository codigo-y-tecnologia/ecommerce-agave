<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

trait InputSanitizer
{
    /**
     * Limpia y valida si los campos contienen contenido potencialmente peligroso.
     * Registra intentos sospechosos en un log dedicado a seguridad.
     *
     * @param  array  &$data  Campos a limpiar y validar
     * @param  array  $palabrasReservadas  Palabras SQL reservadas a bloquear
     * @return void
     */
    public function verificarYLimpiar(array &$data, array $palabrasReservadas): void
    {
        $ip = request()->ip();
        $userEmail = Auth::check() ? Auth::user()->vEmail : 'invitado';

        foreach ($data as $campo => &$valor) {
            if (!is_string($valor)) continue;

            // Limpieza básica
            $valorOriginal = $valor;
            $valor = trim(strip_tags($valor));
            $valor = preg_replace('/[\x00-\x1F\x7F]/u', '', $valor);
            $valor = preg_replace('/\s+/', ' ', $valor);

            // Detectar palabras SQL reservadas (palabra completa)
            foreach ($palabrasReservadas as $palabra) {
                if (preg_match('/\b' . preg_quote($palabra, '/') . '\b/i', $valor)) {
                    Log::channel('security')->warning('Intento de inyección SQL detectado', [
                        'campo' => $campo,
                        'valor_introducido' => $valorOriginal,
                        'palabra_detectada' => $palabra,
                        'ip' => $ip,
                        'usuario' => $userEmail,
                        'ruta' => request()->path(),
                        'fecha' => now()->toDateTimeString(),
                    ]);
                    abort(400, "El campo '$campo' contiene contenido no permitido.");
                }
            }

            // Longitud excesiva (payloads)
            if (strlen($valor) > 255) {
                Log::channel('security')->warning('Campo excesivamente largo detectado', [
                    'campo' => $campo,
                    'longitud' => strlen($valor),
                    'ip' => $ip,
                    'usuario' => $userEmail,
                    'ruta' => request()->path(),
                ]);
                abort(400, "El campo '$campo' es demasiado largo.");
            }

            // Patrón de comentarios o código sospechoso
            if (preg_match('/(--|#|;|\/\*|\*\/|<\?|<script|<\/script>)/i', $valor)) {
                Log::channel('security')->warning('Contenido sospechoso detectado en campo', [
                    'campo' => $campo,
                    'valor_introducido' => $valorOriginal,
                    'ip' => $ip,
                    'usuario' => $userEmail,
                    'ruta' => request()->path(),
                    'fecha' => now()->toDateTimeString(),
                ]);
                abort(400, "El campo '$campo' contiene caracteres no permitidos.");
            }
        }
    }
}
