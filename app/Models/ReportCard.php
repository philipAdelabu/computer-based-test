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
        'total_score',
        'average_score',
        'grade',
        'remarks',
        'position',
        'total_students',
    ];

    protected $casts = [
        'subject_scores' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class);
    }
}