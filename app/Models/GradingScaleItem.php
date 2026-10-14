<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradingScaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'grading_scale_id',
        'grade',
        'min_score',
        'max_score',
        'remark',
        'color',
        'order',
    ];

    protected $casts = [
        'min_score' => 'decimal:2',
        'max_score' => 'decimal:2',
    ];

    public function scale()
    {
        return $this->belongsTo(GradingScale::class, 'grading_scale_id');
    }

    public function getRangeAttribute()
    {
        return number_format($this->min_score, 0) . ' - ' . number_format($this->max_score, 0);
    }
}
