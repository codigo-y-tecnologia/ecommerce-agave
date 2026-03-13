<?php

use App\Services\System\SettingsService;

if (!function_exists('setting')) {

    function setting($key, $default = null)
    {
        return app(SettingsService::class)->get($key, $default);
    }
}
