<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subject_id',
        'duration_minutes',
        'total_questions',
        'total_score',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
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

    public function isActive()
    {
        $now = now();
        return $this->status === 'active' && 
               $now->between($this->start_date, $this->end_date);
    }

    public function isUpcoming()
    {
        return $this->status === 'upcoming' && now()->lt($this->start_date);
    }

    public function isCompleted()
    {
        return $this->status === 'completed' || now()->gt($this->end_date);
    }
}