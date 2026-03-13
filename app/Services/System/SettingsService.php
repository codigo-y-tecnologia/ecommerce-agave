<?php

namespace App\Services\System;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    public function all()
    {
        return Cache::rememberForever('system_settings', function () {
            return Setting::pluck('value', 'key')->toArray();
        });
    }

    public function get($key, $default = null)
    {
        $settings = $this->all();

        return $settings[$key] ?? $default;
    }

    public function set($key, $value)
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget('system_settings');
    }
}
