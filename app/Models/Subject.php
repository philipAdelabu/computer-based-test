<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'class_id',
        'teacher_id',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Relationships
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'subject_id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'subject_id');
    }

    public function results()
    {
        return $this->hasMany(Result::class, 'subject_id');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        return $this->status === 'active' 
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-danger">Inactive</span>';
    }

    public function getTeacherNameAttribute()
    {
        return $this->teacher ? $this->teacher->name : 'Not Assigned';
    }

    public function getClassNameAttribute()
    {
        return $this->class ? $this->class->name : 'Not Assigned';
    }

    public function getQuestionsCountAttribute()
    {
        return $this->questions()->count();
    }

    public function getExamsCountAttribute()
    {
        return $this->exams()->count();
    }

    // Scope for active subjects
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope for subjects by class
    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    // Scope for subjects by teacher
    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }
}