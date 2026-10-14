<?php

namespace App\Services;

use App\Models\GradingScale;

class GradeService
{
    protected $scale;

    public function __construct()
    {
        $this->scale = GradingScale::getDefault();
    }

    /**
     * Determine the grade for a score
     */
    public function getGrade($score)
    {
        if (!$this->scale) {
            return $this->fallbackGrade($score);
        }
        return $this->scale->getGradeLetter($score);
    }

    /**
     * Determine the remark for a score
     */
    public function getRemark($score)
    {
        if (!$this->scale) {
            return $this->fallbackRemark($score);
        }
        return $this->scale->getRemark($score);
    }

    /**
     * Determine the badge color for a score
     */
    public function getColor($score)
    {
        if (!$this->scale) {
            return $this->fallbackColor($score);
        }
        return $this->scale->getColor($score);
    }

    /**
     * Get all grades in the scale
     */
    public function getAllGrades()
    {
        if (!$this->scale) {
            return collect();
        }
        return $this->scale->items;
    }

    /**
     * Fallback grading if no scale is configured
     */
    private function fallbackGrade($score)
    {
        if ($score >= 80) return 'A';
        if ($score >= 70) return 'B';
        if ($score >= 60) return 'C';
        if ($score >= 50) return 'D';
        if ($score >= 40) return 'E';
        return 'F';
    }

    private function fallbackRemark($score)
    {
        if ($score >= 80) return 'Excellent';
        if ($score >= 70) return 'Very Good';
        if ($score >= 60) return 'Good';
        if ($score >= 50) return 'Fair';
        if ($score >= 40) return 'Poor';
        return 'Very Poor';
    }

    private function fallbackColor($score)
    {
        if ($score >= 80) return 'success';
        if ($score >= 70) return 'primary';
        if ($score >= 60) return 'info';
        if ($score >= 50) return 'warning';
        if ($score >= 40) return 'warning';
        return 'danger';
    }
}