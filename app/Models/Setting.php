<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
    ];

    /**
     * Get a setting value by key
     */
    public static function get($key, $default = null)
    {
        $settings = self::getAllCached();
        
        return $settings[$key] ?? $default;
    }

    /**
     * Set a setting value
     */
    public static function set($key, $value, $type = 'string')
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );
        
        Cache::forget('app_settings');
        
        return $setting;
    }

    /**
     * Get all settings as key => value array
     */
    public static function getAllCached()
    {
        return Cache::rememberForever('app_settings', function () {
            return self::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Get the full URL for image-type settings
     */
    public static function getImageUrl($key, $default = null)
    {
        $value = self::get($key);
        
        if (!$value) {
            return $default;
        }
        
        // Check if file exists in public folder
        $publicPath = public_path($value);
        if (file_exists($publicPath)) {
            return asset($value);
        }
        
        // Fallback: check storage folder
        $storagePath = storage_path('app/public/' . $value);
        if (file_exists($storagePath)) {
            return asset('storage/' . $value);
        }
        
        return $default;
    }

    /**
     * Clear settings cache
     */
    public static function clearCache()
    {
        Cache::forget('app_settings');
    }
}