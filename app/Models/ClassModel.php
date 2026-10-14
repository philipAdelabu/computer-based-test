<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'code',
        'description',
        'class_teacher_id',
    ];

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'class_id'); // Specify foreign key
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id'); // Specify foreign key
    }

    public function questions()
    {
        return $this->hasManyThrough(Question::class, Subject::class, 'class_id', 'subject_id');
    }
    // Add the relationship
    public function classTeacher()
    {
        return $this->belongsTo(User::class, 'class_teacher_id');
    }

    // Add a helper accessor
    public function getClassTeacherNameAttribute()
    {
        return $this->classTeacher ? $this->classTeacher->name : 'Not Assigned';
    }
}