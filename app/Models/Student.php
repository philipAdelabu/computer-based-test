<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'class_id', // Make sure this matches your migration
        'admission_number',
        'date_of_birth',
        'guardian_name',
        'guardian_phone',
        'status',
        'can_take_assessments',   // NEW
        'deactivation_reason',     // NEW
        'deactivated_by',          // NEW
        'deactivated_at',          // NEW
        'reactivate_at',           // NEW
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'can_take_assessments' => 'boolean',   // NEW
        'deactivated_at' => 'datetime',        // NEW
        'reactivate_at' => 'datetime',         // NEW
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id'); // Specify foreign key
    }

    public function examAttempts()
    {
        return $this->hasMany(ExamAttempt::class, 'student_id');
    }

    public function results()
    {
        return $this->hasMany(Result::class, 'student_id');
    }

    public function reportCards()
    {
        return $this->hasMany(ReportCard::class, 'student_id');
    }

    public function getFullNameAttribute()
    {
        return $this->user->name;
    }

    public function getEmailAttribute()
    {
        return $this->user->email;
    }

     /**
 * Who deactivated this student
 */
public function deactivator()
{
    return $this->belongsTo(User::class, 'deactivated_by');
}

// ============ NEW HELPER METHODS ============

/**
 * Check if the student can take assessments
 */
public function getIsAssessmentActiveAttribute()
{
    // If explicitly deactivated
    if (!$this->can_take_assessments) {
        // Check if there's a scheduled reactivation date that has passed
        if ($this->reactivate_at && now()->gte($this->reactivate_at)) {
            // Auto-reactivate
            $this->update([
                'can_take_assessments' => true,
                'deactivation_reason' => null,
                'deactivated_by' => null,
                'deactivated_at' => null,
                'reactivate_at' => null,
            ]);
            return true;
        }
        return false;
    }

    // Also consider the overall student status
    if ($this->status !== 'active') {
        return false;
    }

    return true;
}

/**
 * Get the deactivation status badge
 */
public function getAssessmentAccessBadgeAttribute()
{
    if ($this->is_assessment_active) {
        return '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Active</span>';
    }
    return '<span class="badge bg-danger"><i class="bi bi-x-circle"></i> Deactivated</span>';
}

/**
 * Deactivate the student from assessments
 */
public function deactivateAssessments($reason, $deactivatedBy = null, $reactivateAt = null)
{
    $this->update([
        'can_take_assessments' => false,
        'deactivation_reason' => $reason,
        'deactivated_by' => $deactivatedBy ?? auth()->id(),
        'deactivated_at' => now(),
        'reactivate_at' => $reactivateAt,
    ]);
}

/**
 * Reactivate the student for assessments
 */
public function reactivateAssessments()
{
    $this->update([
        'can_take_assessments' => true,
        'deactivation_reason' => null,
        'deactivated_by' => null,
        'deactivated_at' => null,
        'reactivate_at' => null,
    ]);
}
}