<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCard extends Model
{
    use HasFactory;

   // app/Models/ReportCard.php
protected $fillable = [
    'student_id',
    'class_id',
    'term',
    'academic_year',
    'session',                    // NEW
    'gender',                     // NEW
    'age',                        // NEW
    'house',                      // NEW
    'no_in_class',                // NEW
    'subject_scores',
    'subject_breakdown',
    'total_score',
    'average_score',
    'grade',
    'remarks',
    'position',
    'total_students',
    'total_test_score',
    'total_test_max',
    'total_exam_score',
    'total_exam_max',
    'grand_total',
    'grand_max',
    'max_class_score',            // NEW
    'min_class_score',            // NEW
    'marks_obtainable',           // NEW
    'times_school_opened',        // NEW
    'times_present',              // NEW
    'affective_traits',           // NEW
    'psychomotor_skills',         // NEW
    'class_teacher_comment',      // NEW
    'class_teacher_name',         // NEW
    'principal_comment',          // NEW
    'principal_name',             // NEW
    'vacation_date',              // NEW
    'resumption_date',            // NEW
    'generated_date',
    'generated_by',
];

protected $casts = [
    'subject_scores' => 'array',
    'subject_breakdown' => 'array',
    'affective_traits' => 'array',      // NEW
    'psychomotor_skills' => 'array',    // NEW
    'generated_date' => 'date',
    'vacation_date' => 'date',          // NEW
    'resumption_date' => 'date',        // NEW
  ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function getOverallGradeAttribute()
    {
        $percentage = $this->grand_max > 0 
            ? ($this->grand_total / $this->grand_max) * 100 
            : 0;
        
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B';
        if ($percentage >= 60) return 'C';
        if ($percentage >= 50) return 'D';
        if ($percentage >= 40) return 'E';
        return 'F';
    }

    public function getOverallPercentageAttribute()
    {
        return $this->grand_max > 0 
            ? round(($this->grand_total / $this->grand_max) * 100, 2) 
            : 0;
    }
}