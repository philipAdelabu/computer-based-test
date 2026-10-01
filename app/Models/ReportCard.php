<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_id',
        'term',
        'academic_year',
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
        'generated_date',
        'generated_by',
    ];

    protected $casts = [
        'subject_scores' => 'array',
        'subject_breakdown' => 'array',
        'generated_date' => 'date',
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