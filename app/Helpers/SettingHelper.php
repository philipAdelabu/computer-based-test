<?php

namespace App\Helpers;

use App\Models\Setting;

class SettingHelper
{
    /**
     * Get a setting value
     */
    public static function get($key, $default = null)
    {
        return Setting::get($key, $default);
    }

    /**
     * Get the school name
     */
    public static function schoolName()
    {
        return Setting::get('school_name', config('app.name', 'CBT System'));
    }

    /**
     * Get the school logo URL
     */
    public static function schoolLogo()
    {
        return Setting::getImageUrl('school_logo');
    }

    /**
     * Get the school motto/slogan
     */
    public static function schoolMotto()
    {
        return Setting::get('school_motto', 'Excellence in Education');
    }

    /**
     * Get the school address
     */
    public static function schoolAddress()
    {
        return Setting::get('school_address', '');
    }

    /**
     * Get the school phone
     */
    public static function schoolPhone()
    {
        return Setting::get('school_phone', '');
    }

    /**
     * Get the school email
     */
    public static function schoolEmail()
    {
        return Setting::get('school_email', '');
    }
}