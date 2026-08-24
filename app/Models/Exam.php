<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'subject_id',
        'duration_minutes',
        'total_questions',
        'total_score',
        'start_date',
        'end_date',
        'status',
        'created_by_role',
        'created_by',
        'instructions',
        'is_published',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_published' => 'boolean',
    ];

    // Relationships
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'exam_questions')
                    ->withPivot('question_order')
                    ->orderBy('pivot_question_order');
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('is_published', true);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming')
                    ->where('start_date', '>', now());
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'active')
                    ->where('is_published', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $statuses = [
            'upcoming' => 'badge bg-info',
            'active' => 'badge bg-success',
            'completed' => 'badge bg-secondary',
            'cancelled' => 'badge bg-danger',
        ];
        return $statuses[$this->status] ?? 'badge bg-secondary';
    }

    public function getStatusTextAttribute()
    {
        return ucfirst($this->status);
    }



    // Methods
    public function hasStudentAttempted($studentId)
    {
        return $this->attempts()
                    ->where('student_id', $studentId)
                    ->exists();
    }

    public function getStudentAttempt($studentId)
    {
        return $this->attempts()
                    ->where('student_id', $studentId)
                    ->first();
    }

    public function getTotalStudentsAttribute()
    {
        return $this->attempts()->distinct('student_id')->count();
    }

    public function getAverageScoreAttribute()
    {
        return $this->attempts()
                    ->where('status', 'submitted')
                    ->avg('score') ?? 0;
    }

    public function getPassRateAttribute()
    {
        $total = $this->attempts()->where('status', 'submitted')->count();
        if ($total === 0) return 0;
        
        $passed = $this->attempts()
                       ->where('status', 'submitted')
                       ->where('score', '>=', $this->total_score * 0.5)
                       ->count();
        
        return round(($passed / $total) * 100);
    }



      public function getFormattedStartDateAttribute()
    {
        return $this->start_date->timezone(config('app.timezone'))->format('F d, Y h:i A');
    }

    public function getFormattedEndDateAttribute()
    {
        return $this->end_date->timezone(config('app.timezone'))->format('F d, Y h:i A');
    }

    // Get dates for JavaScript (ISO format with timezone)
    public function getStartDateIsoAttribute()
    {
        return $this->start_date->timezone(config('app.timezone'))->toISOString();
    }

    public function getEndDateIsoAttribute()
    {
        return $this->end_date->timezone(config('app.timezone'))->toISOString();
    }

    // Check if exam is active in user's timezone
    public function getIsActiveAttribute()
    {
        $now = Carbon::now(config('app.timezone'));
        $start = $this->start_date->timezone(config('app.timezone'));
        $end = $this->end_date->timezone(config('app.timezone'));
        
        return $this->status === 'active' && 
               $this->is_published &&
               $now->between($start, $end);
    }

    public function getIsUpcomingAttribute()
    {
        $now = Carbon::now(config('app.timezone'));
        $start = $this->start_date->timezone(config('app.timezone'));
        
        return $this->status === 'upcoming' && 
               $this->is_published &&
               $now->lt($start);
    }

    public function getIsCompletedAttribute()
    {
        $now = Carbon::now(config('app.timezone'));
        $end = $this->end_date->timezone(config('app.timezone'));
        
        return $this->status === 'completed' || 
               $now->gt($end);
    }

    public function getRemainingTimeAttribute()
    {
        if ($this->is_active) {
            $now = Carbon::now(config('app.timezone'));
            $end = $this->end_date->timezone(config('app.timezone'));
            
            if ($now->lt($end)) {
                return $now->diffInSeconds($end);
            }
        }
        return 0;
    }

    // Get time remaining in human readable format
    public function getRemainingTimeHumanAttribute()
    {
        $seconds = $this->remaining_time;
        if ($seconds <= 0) return 'Expired';
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        
        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
        }
        return sprintf('%02d:%02d', $minutes, $secs);
    }

    // Check if student can take exam with timezone consideration
    public function canStudentTake($studentId)
    {
        $now = Carbon::now(config('app.timezone'));
        $start = $this->start_date->timezone(config('app.timezone'));
        $end = $this->end_date->timezone(config('app.timezone'));
        
        // Check if exam is active and published
        if (!$this->is_active) {
            if ($now->lt($start)) {
                return ['can' => false, 'reason' => 'This exam will start on ' . $this->formatted_start_date];
            }
            if ($now->gt($end)) {
                return ['can' => false, 'reason' => 'This exam has already ended.'];
            }
            return ['can' => false, 'reason' => 'Exam is not currently available.'];
        }

        // Check if student is in the subject's class
        $student = Student::find($studentId);
        if (!$student || $student->class_id !== $this->subject->class_id) {
            return ['can' => false, 'reason' => 'You are not enrolled in this subject.'];
        }

        // Check if student has already attempted
        if ($this->hasStudentAttempted($studentId)) {
            $attempt = $this->getStudentAttempt($studentId);
            if ($attempt->status === 'submitted') {
                return ['can' => false, 'reason' => 'You have already completed this exam.'];
            }
            if ($attempt->status === 'in_progress') {
                return ['can' => true, 'reason' => 'Continue your exam.', 'attempt' => $attempt];
            }
        }

        return ['can' => true, 'reason' => 'You can take this exam.'];
    }



}