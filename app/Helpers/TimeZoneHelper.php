<?php

namespace App\Helpers;

use Carbon\Carbon;

class TimezoneHelper
{
    /**
     * Get the current time in the configured timezone
     */
    public static function now()
    {
        return Carbon::now(config('app.timezone'));
    }

    /**
     * Convert a date to the configured timezone
     */
    public static function convert($date)
    {
        return Carbon::parse($date)->timezone(config('app.timezone'));
    }

    /**
     * Format a date for display
     */
    public static function format($date, $format = 'F d, Y h:i A')
    {
        return Carbon::parse($date)->timezone(config('app.timezone'))->format($format);
    }

    /**
     * Get timezone offset in hours
     */
    public static function getOffset()
    {
        return Carbon::now(config('app.timezone'))->getOffset() / 3600;
    }

    /**
     * Get timezone abbreviation
     */
    public static function getAbbreviation()
    {
        return Carbon::now(config('app.timezone'))->getTimezone()->getAbbr();
    }
}