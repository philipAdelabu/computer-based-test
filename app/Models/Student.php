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
    ];

    protected $casts = [
        'date_of_birth' => 'date',
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
}