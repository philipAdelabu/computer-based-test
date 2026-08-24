<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SetTimezone
{
    public function handle(Request $request, Closure $next)
    {
        // Get timezone from user preference, session, or default
        $timezone = session('timezone', config('app.timezone'));
        
        // Set the timezone for Carbon
        Carbon::setTimezone($timezone);
        date_default_timezone_set($timezone);
        
        return $next($request);
    }
}