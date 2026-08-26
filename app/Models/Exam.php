<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Exam extends Model
{
    use HasFactory;

     const SCHEDULE_NO_DATE = 'no_date';
    const SCHEDULE_SINGLE_DATE = 'single_date';
    const SCHEDULE_DATE_RANGE = 'date_range';

   protected $fillable = [
    'title',
    'description',
    'subject_id',
    'duration_minutes',
    'total_questions',
    'total_score',
    'schedule_type',        // Add this
    'start_date',
    'end_date',
    'available_from',       // Add this
    'available_to',         // Add this
    'status',
    'created_by_role',
    'created_by',
    'instructions',
    'is_published',
    'max_attempts',         // Add this
    'passing_score',        // Add this
    'show_answers_after_completion', // Add this
];

protected $casts = [
    'start_date' => 'datetime',
    'end_date' => 'datetime',
    'available_from' => 'datetime',
    'available_to' => 'datetime',
    'is_published' => 'boolean',
    'show_answers_after_completion' => 'boolean',
    'max_attempts' => 'integer',
    'passing_score' => 'integer',
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
         if (!$this->start_date) {
            return null;
        }
        return $this->start_date->timezone(config('app.timezone'))->format('F d, Y h:i A');
    }

    public function getFormattedEndDateAttribute()
    {
         if (!$this->end_date) {
            return null;
        }
        return $this->end_date->timezone(config('app.timezone'))->format('F d, Y h:i A');
    }

    // Get dates for JavaScript (ISO format with timezone)
    public function getStartDateIsoAttribute()
    {
         if (!$this->start_date) {
            return null;
        }
        return $this->start_date->timezone(config('app.timezone'))->toISOString();
    }

    public function getEndDateIsoAttribute()
    {
         if (!$this->end_date) {
            return null;
        }
        return $this->end_date->timezone(config('app.timezone'))->toISOString();
    }

     // Get available from date
    public function getFormattedAvailableFromAttribute()
    {
        if (!$this->available_from) {
            return 'Not Set';
        }
        return $this->available_from->timezone(config('app.timezone'))->format('F d, Y h:i A');
    }

    // Get available to date
    public function getFormattedAvailableToAttribute()
    {
        if (!$this->available_to) {
            return 'Not Set';
        }
        return $this->available_to->timezone(config('app.timezone'))->format('F d, Y h:i A');
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

    
     // Check if exam is available for students
    public function getIsAvailableAttribute()
    {
        if (!$this->is_published) {
            return false;
        }

        $now = Carbon::now(config('app.timezone'));

        switch ($this->schedule_type) {
            case self::SCHEDULE_NO_DATE:
                // Always available
                return $this->status === 'active';

            case self::SCHEDULE_SINGLE_DATE:
                if (!$this->start_date) return false;
                $start = $this->start_date->timezone(config('app.timezone'));
                $end = $this->end_date ? $this->end_date->timezone(config('app.timezone')) : $start->copy()->addDay();
                return $this->status === 'active' && $now->between($start, $end);

            case self::SCHEDULE_DATE_RANGE:
                if (!$this->available_from || !$this->available_to) return false;
                $from = $this->available_from->timezone(config('app.timezone'));
                $to = $this->available_to->timezone(config('app.timezone'));
                return $this->status === 'active' && $now->between($from, $to);

            default:
                return false;
        }
    }

    // Get formatted availability text
    public function getAvailabilityTextAttribute()
    {
        switch ($this->schedule_type) {
            case self::SCHEDULE_NO_DATE:
                return 'Always Available';

            case self::SCHEDULE_SINGLE_DATE:
                if ($this->start_date && $this->end_date) {
                    return $this->formatted_start_date . ' - ' . $this->formatted_end_date;
                } elseif ($this->start_date) {
                    return $this->formatted_start_date;
                }
                return 'Date not set';

            case self::SCHEDULE_DATE_RANGE:
                if ($this->available_from && $this->available_to) {
                    return $this->available_from->timezone(config('app.timezone'))->format('M d, Y') . ' - ' . 
                           $this->available_to->timezone(config('app.timezone'))->format('M d, Y');
                }
                return 'Date range not set';

            default:
                return 'Not available';
        }
    }

    // Get the exam status with availability
    public function getStatusWithAvailabilityAttribute()
    {
        if (!$this->is_published) {
            return ['status' => 'draft', 'label' => 'Draft', 'color' => 'secondary'];
        }

        if ($this->status === 'completed') {
            return ['status' => 'completed', 'label' => 'Completed', 'color' => 'secondary'];
        }

        if ($this->status === 'cancelled') {
            return ['status' => 'cancelled', 'label' => 'Cancelled', 'color' => 'danger'];
        }

        if ($this->is_available) {
            return ['status' => 'available', 'label' => 'Available', 'color' => 'success'];
        }

        // Check if it's upcoming
        $now = Carbon::now(config('app.timezone'));
        $start = $this->start_date ? $this->start_date->timezone(config('app.timezone')) : null;
        $availableFrom = $this->available_from ? $this->available_from->timezone(config('app.timezone')) : null;

        if (($start && $now->lt($start)) || ($availableFrom && $now->lt($availableFrom))) {
            return ['status' => 'upcoming', 'label' => 'Upcoming', 'color' => 'info'];
        }

        // Check if it's expired
        $end = $this->end_date ? $this->end_date->timezone(config('app.timezone')) : null;
        $availableTo = $this->available_to ? $this->available_to->timezone(config('app.timezone')) : null;

        if (($end && $now->gt($end)) || ($availableTo && $now->gt($availableTo))) {
            return ['status' => 'expired', 'label' => 'Expired', 'color' => 'warning'];
        }

        return ['status' => 'inactive', 'label' => 'Inactive', 'color' => 'secondary'];
    }

  

  // app/Models/Exam.php - Update the canStudentTake method

public function canStudentTake($studentId)
{
    // Check if exam is published
    if (!$this->is_published) {
        return ['can' => false, 'reason' => 'This exam is not published yet.'];
    }

    // Check if exam is active
    if ($this->status !== 'active') {
        return ['can' => false, 'reason' => 'This exam is not active.'];
    }

    // Check availability
    if (!$this->is_available) {
        $now = Carbon::now(config('app.timezone'));
        
        if ($this->schedule_type === self::SCHEDULE_SINGLE_DATE && $this->start_date) {
            $start = $this->start_date->timezone(config('app.timezone'));
            if ($now->lt($start)) {
                return ['can' => false, 'reason' => 'This exam will start on ' . $this->formatted_start_date];
            }
        }

        if ($this->schedule_type === self::SCHEDULE_DATE_RANGE && $this->available_from) {
            $from = $this->available_from->timezone(config('app.timezone'));
            if ($now->lt($from)) {
                return ['can' => false, 'reason' => 'This exam will be available from ' . $this->availability_text];
            }
        }

        return ['can' => false, 'reason' => 'This exam is not currently available.'];
    }

    // Check if student is in the subject's class
    $student = Student::find($studentId);
    if (!$student || $student->class_id !== $this->subject->class_id) {
        return ['can' => false, 'reason' => 'You are not enrolled in this subject.'];
    }

    // Check attempts
    $attemptsCount = $this->attempts()->where('student_id', $studentId)->count();
    
    // Check max attempts
    if ($this->max_attempts > 0 && $attemptsCount >= $this->max_attempts) {
        // Check if any attempt is in progress
        $inProgress = $this->attempts()
            ->where('student_id', $studentId)
            ->where('status', 'in_progress')
            ->first();
        
        if ($inProgress) {
            return ['can' => true, 'reason' => 'Continue your exam.', 'attempt' => $inProgress];
        }
        
        return ['can' => false, 'reason' => 'You have reached the maximum number of attempts (' . $this->max_attempts . ').'];
    }

    // Check for in-progress attempt
    $inProgress = $this->attempts()
        ->where('student_id', $studentId)
        ->where('status', 'in_progress')
        ->first();

    if ($inProgress) {
        return ['can' => true, 'reason' => 'Continue your exam.', 'attempt' => $inProgress];
    }

    return ['can' => true, 'reason' => 'You can take this exam.'];
}

    // app/Models/Exam.php

public function getDisplayDateAttribute()
{
    switch ($this->schedule_type) {
        case 'no_date':
            return 'Always Available';
        case 'single_date':
            $date = $this->formatted_start_date;
            if ($this->end_date) {
                $date .= ' - ' . $this->formatted_end_date;
            }
            return $date;
        case 'date_range':
            if ($this->available_from && $this->available_to) {
                return $this->available_from->timezone(config('app.timezone'))->format('M d, Y h:i A') . 
                       ' - ' . 
                       $this->available_to->timezone(config('app.timezone'))->format('M d, Y h:i A');
            }
            return 'Date range not set';
        default:
            return 'Not set';
    }
}

public function getShortDisplayDateAttribute()
{
    switch ($this->schedule_type) {
        case 'no_date':
            return 'Always Available';
        case 'single_date':
            return $this->start_date ? $this->start_date->timezone(config('app.timezone'))->format('M d, Y') : 'Not set';
        case 'date_range':
            if ($this->available_from && $this->available_to) {
                return $this->available_from->timezone(config('app.timezone'))->format('M d') . 
                       ' - ' . 
                       $this->available_to->timezone(config('app.timezone'))->format('M d, Y');
            }
            return 'Not set';
        default:
            return 'Not set';
    }
}


}