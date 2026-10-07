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
}