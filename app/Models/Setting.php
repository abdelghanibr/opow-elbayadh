<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key (cached for 1 hour)
     */
    public static function get(string $key, $default = null)
    {
        $settings = Cache::remember('settings_all', 3600, function () {
            return static::pluck('value', 'key')->toArray();
        });

        return $settings[$key] ?? $default;
    }

    /**
     * Set or update a setting value
     */
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('settings_all');
    }

    /**
     * Get all settings as key => value array
     */
    public static function allArray(): array
    {
        return Cache::remember('settings_all', 3600, function () {
            return static::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Flush settings cache
     */
    public static function flushCache(): void
    {
        Cache::forget('settings_all');
    }
}
