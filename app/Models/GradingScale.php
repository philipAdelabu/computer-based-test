<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class GradingScale extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(GradingScaleItem::class)->orderBy('order');
    }

    // ============ HELPERS ============

    /**
     * Get the default active grading scale (cached)
     */
    public static function getDefault()
    {
        return Cache::remember('default_grading_scale', 3600, function () {
            // Try to get the default one
            $scale = self::with('items')
                ->where('is_default', true)
                ->where('is_active', true)
                ->first();

            // Fallback to first active
            if (!$scale) {
                $scale = self::with('items')
                    ->where('is_active', true)
                    ->first();
            }

            return $scale;
        });
    }

    /**
     * Find the grade for a given score (percentage)
     */
    public function getGradeForScore($score)
    {
        $score = (float) $score;
        
        foreach ($this->items as $item) {
            if ($score >= $item->min_score && $score <= $item->max_score) {
                return $item;
            }
        }
        
        // If no match, return a null item
        return null;
    }

    /**
     * Get grade letter for a score
     */
    public function getGradeLetter($score)
    {
        $item = $this->getGradeForScore($score);
        return $item ? $item->grade : 'N/A';
    }

    /**
     * Get remark for a score
     */
    public function getRemark($score)
    {
        $item = $this->getGradeForScore($score);
        return $item ? $item->remark : 'N/A';
    }

    /**
     * Get color for a score
     */
    public function getColor($score)
    {
        $item = $this->getGradeForScore($score);
        return $item ? $item->color : 'secondary';
    }

    /**
     * Clear cache on save
     */
    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('default_grading_scale');
        });
        static::deleted(function () {
            Cache::forget('default_grading_scale');
        });
    }
}