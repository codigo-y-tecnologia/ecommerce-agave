<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Configuracion;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;

class ConfiguracionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $configArray = Cache::remember('configuraciones_sistema', 3600, function () {

            $configuraciones = Configuracion::all();

            $array = [];

            foreach ($configuraciones as $config) {
                $array[$config->clave] = $config->valor;
            }

            return $array;
        });

        Config::set('tienda', $configArray);
    }
}
