<?php

use App\Models\Configuracion;

if (!function_exists('config_sistema')) {
    
    function config_sistema($clave)
    {
        return Configuracion::where('clave', $clave)->value('valor');
    }
}
