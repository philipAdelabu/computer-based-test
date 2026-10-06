<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'question_text',
        'image_path',
        'options',
        'correct_answer',
        'score',
        'difficulty',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_questions')
                    ->withPivot('question_order')
                    ->orderBy('pivot_question_order');
    }


    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return null;
        }
        
        // Check if the image is in the public folder
        $publicPath = public_path($this->image_path);
        if (file_exists($publicPath)) {
            return asset($this->image_path);
        }
        
        // Fallback: check the storage folder
        $storagePath = storage_path('app/public/' . $this->image_path);
        if (file_exists($storagePath)) {
            return asset('storage/' . $this->image_path);
        }
        
        return null;
    }

  
    public function getHasImageAttribute()
    {
        return $this->image_url !== null;
    }

}