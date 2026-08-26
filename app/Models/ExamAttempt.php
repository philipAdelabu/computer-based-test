<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'student_id',
        'attempt_number',
        'started_at',
        'completed_at',
        'answers',
        'score',
        'total_questions_answered',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'answers' => 'array',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function getTimeRemainingAttribute()
    {
        if (!$this->started_at || $this->status === 'submitted') {
            return 0;
        }
        
          $started = $this->started_at->timezone(config('app.timezone'));
          $duration = $this->exam->duration_minutes;
          $endTime = $started->addMinutes($duration);
          $now = Carbon::now(config('app.timezone'));
        
        if ($now->gt($endTime)) {
            return 0;
        }
        
        return $endTime->diffInSeconds($now);
    }

    public function getTimeSpentAttribute()
    {
        if (!$this->started_at) {
            return 0;
        }
        
         $end = $this->completed_at ?? Carbon::now(config('app.timezone'));
         return $this->started_at->timezone(config('app.timezone'))->diffInSeconds($end->timezone(config('app.timezone')));
    }
}