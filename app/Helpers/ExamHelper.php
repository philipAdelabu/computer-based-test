<?php

namespace App\Helpers;

use App\Models\Exam;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ExamHelper
{
    public static function getAvailableExamsCount()
    {
        if (!Auth::check() || !Auth::user()->isStudent()) {
            return 0;
        }
        
        $student = Auth::user()->student;
        
        return Exam::where('is_published', true)
            ->where('status', 'active')
            ->whereHas('subject', function($query) use ($student) {
                $query->where('class_id', $student->class_id);
            })
            ->where(function($query) {
                $now = Carbon::now(config('app.timezone'));
                $query->where('schedule_type', 'no_date')
                    ->orWhere(function($q) use ($now) {
                        $q->where('schedule_type', 'single_date')
                            ->where('start_date', '<=', $now)
                            ->where(function($sub) use ($now) {
                                $sub->whereNull('end_date')
                                    ->orWhere('end_date', '>=', $now);
                            });
                    })
                    ->orWhere(function($q) use ($now) {
                        $q->where('schedule_type', 'date_range')
                            ->where('available_from', '<=', $now)
                            ->where('available_to', '>=', $now);
                    });
            })
            ->count();
    }
}