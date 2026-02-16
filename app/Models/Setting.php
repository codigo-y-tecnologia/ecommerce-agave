<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'tbl_settings';

    protected $fillable = [
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'boolean',
    ];

    public $timestamps = true;

    public static function getValue(string $key, $default = null)
    {
        return static::where('key', $key)->value('value') ?? $default;
    }
}
