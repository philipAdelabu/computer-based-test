<?php

namespace App\Services;

use App\Models\ReportCard;
use App\Models\ExamAttempt;
use Illuminate\Support\Facades\Log;

class ReportCardAutoFillService
{
    /**
     * Affective Traits list
     */
    public const AFFECTIVE_TRAITS = [
        'Punctuality',
        'Mental Alertness',
        'Respect',
        'Neatness',
        'Politeness',
        'Honesty',
        'Peer Relationship',
        'Willingness To Learn',
        'Teamwork',
    ];

    /**
     * Psychomotor Skills list
     */
    public const PSYCHOMOTOR_SKILLS = [
        'Verbal Skills',
        'Sports & Games',
        'Artistic Skills',
        'Music',
        'Dance',
        'Entrepreneurship',
        'Chess',
    ];

    /**
     * Auto-fill an entire report card
     */
    public function autoFill(ReportCard $reportCard, bool $overwrite = false): array
    {
        $student = $reportCard->student;
        $user = $student->user;

        // Analyze student performance
        $analysis = $this->analyzeStudent($reportCard, $student);

        $updates = [];

        // ============ BASIC INFO ============
        if ($overwrite || !$reportCard->gender) {
            $updates['gender'] = $this->inferGender($user->name) ?? $reportCard->gender;
        }

        if ($overwrite || !$reportCard->age) {
            $updates['age'] = $student->date_of_birth ? $student->date_of_birth->age : null;
        }

        if ($overwrite || !$reportCard->no_in_class) {
            $updates['no_in_class'] = $reportCard->total_students;
        }

        if ($overwrite || !$reportCard->session) {
            $updates['session'] = $reportCard->academic_year . '/' . ($reportCard->academic_year + 1);
        }

        // ============ RESULT ANALYSIS ============
        if ($overwrite || !$reportCard->marks_obtainable) {
            $updates['marks_obtainable'] = $reportCard->grand_max;
        }

        // Class stats from peer report cards
        $classStats = $this->getClassStats($reportCard);
        if ($overwrite || !$reportCard->max_class_score) {
            $updates['max_class_score'] = $classStats['max'];
        }
        if ($overwrite || !$reportCard->min_class_score) {
            $updates['min_class_score'] = $classStats['min'];
        }

        // ============ ATTENDANCE ============
        if ($overwrite || !$reportCard->times_school_opened) {
            $updates['times_school_opened'] = $this->getSchoolOpenDays($reportCard->term);
        }
        if ($overwrite || !$reportCard->times_present) {
            $updates['times_present'] = $this->estimateAttendance(
                $reportCard->times_school_opened ?? $this->getSchoolOpenDays($reportCard->term),
                $analysis['consistency_score']
            );
        }

        // ============ AFFECTIVE TRAITS ============
        if ($overwrite || empty($reportCard->affective_traits)) {
            $updates['affective_traits'] = $this->generateAffectiveTraits($analysis);
        }

        // ============ PSYCHOMOTOR SKILLS ============
        if ($overwrite || empty($reportCard->psychomotor_skills)) {
            $updates['psychomotor_skills'] = $this->generatePsychomotorSkills($analysis, $user->name);
        }

        // ============ COMMENTS ============
        if ($overwrite || !$reportCard->class_teacher_comment) {
            $updates['class_teacher_comment'] = $this->generateTeacherComment($analysis, $user->name);
        }

        if ($overwrite || !$reportCard->class_teacher_name) {
            $classTeacher = $reportCard->class->classTeacher ?? null;
            $updates['class_teacher_name'] = $classTeacher ? $classTeacher->name : null;
        }

        if ($overwrite || !$reportCard->principal_comment) {
            $updates['principal_comment'] = $this->generatePrincipalComment($analysis, $user->name);
        }

        // ============ SAVE ============
        $reportCard->update($updates);

        return $updates;
    }

    /**
     * Analyze a student's performance across all subjects
     */
    protected function analyzeStudent(ReportCard $reportCard, $student): array
    {
        $subjects = $reportCard->subject_scores ?? [];
        
        $percentages = [];
        $grades = [];
        
        foreach ($subjects as $subject) {

            if (!($subject['attempted'] ?? true)) {
            continue;
            }
            
            if (isset($subject['percentage']) && $subject['percentage'] !== null) {
                $percentages[] = (float) $subject['percentage'];
            }
            if (isset($subject['grade']) && $subject['grade'] !== null) {
                $grades[] = $subject['grade'];
            }

            if (isset($subject['percentage'])) {
                $percentages[] = (float) $subject['percentage'];
            }
            if (isset($subject['grade'])) {
                $grades[] = $subject['grade'];
            }
        }

        $avgPercentage = !empty($percentages) 
            ? array_sum($percentages) / count($percentages) 
            : 0;

        $maxPercentage = !empty($percentages) ? max($percentages) : 0;
        $minPercentage = !empty($percentages) ? min($percentages) : 0;

        // Consistency: how close is the student to their average?
        $consistency = 5;
        if (count($percentages) > 1) {
            $variance = 0;
            foreach ($percentages as $p) {
                $variance += pow($p - $avgPercentage, 2);
            }
            $variance = $variance / count($percentages);
            $stdDev = sqrt($variance);
            
            // Lower std dev = more consistent = higher score
            if ($stdDev <= 5) $consistency = 5;
            elseif ($stdDev <= 10) $consistency = 4;
            elseif ($stdDev <= 15) $consistency = 3;
            elseif ($stdDev <= 20) $consistency = 2;
            else $consistency = 1;
        }

        return [
            'avg_percentage' => round($avgPercentage, 1),
            'max_percentage' => round($maxPercentage, 1),
            'min_percentage' => round($minPercentage, 1),
            'subject_count' => count($subjects),
            'overall_grade' => $reportCard->grade,
            'position' => $reportCard->position,
            'total_students' => $reportCard->total_students,
            'consistency_score' => $consistency,
            'is_top_performer' => $reportCard->position && $reportCard->total_students 
                ? ($reportCard->position <= max(1, ceil($reportCard->total_students * 0.1)))
                : false,
            'is_bottom_performer' => $reportCard->position && $reportCard->total_students 
                ? ($reportCard->position > $reportCard->total_students * 0.8)
                : false,
            'is_middle_performer' => !$reportCard->position || !$reportCard->total_students 
                ? true
                : !(
                    ($reportCard->position <= max(1, ceil($reportCard->total_students * 0.1)))
                    || ($reportCard->position > $reportCard->total_students * 0.8)
                ),
        ];
    }

    /**
     * Get class statistics (max/min scores of peers)
     */
    protected function getClassStats(ReportCard $reportCard): array
    {
        $peers = ReportCard::where('class_id', $reportCard->class_id)
            ->where('term', $reportCard->term)
            ->where('academic_year', $reportCard->academic_year)
            ->get(['grand_total', 'grand_max']);

        if ($peers->isEmpty()) {
            return ['max' => $reportCard->grand_total, 'min' => $reportCard->grand_total];
        }

        $totals = $peers->pluck('grand_total')->filter();
        
        return [
            'max' => $totals->max() ?? $reportCard->grand_total,
            'min' => $totals->min() ?? $reportCard->grand_total,
        ];
    }

    /**
     * Infer gender from a name (rough heuristic)
     */
    protected function inferGender(string $name): ?string
    {
        // Common Nigerian/African male name endings
        $maleNames = ['a', 'e', 'i', 'o', 'u']; // Placeholder
        
        // Better: try to match common patterns
        $first = strtolower(explode(' ', trim($name))[0] ?? '');
        
        $maleSuffix = ['nuel', 'chid', 'deji', 'kunle', 'ade', 'tunde', 'bola', 'wale', 'dayo'];
        $femaleSuffix = ['chioma', 'adaeze', 'funmi', 'titi', 'yemi', 'bisi', 'kemi', 'tola'];
        
        foreach ($maleSuffix as $pattern) {
            if (str_contains($first, $pattern)) return 'Male';
        }
        foreach ($femaleSuffix as $pattern) {
            if (str_contains($first, $pattern)) return 'Female';
        }
        
        return null;
    }

    /**
     * Get school open days per term
     */
    protected function getSchoolOpenDays($term): int
    {
        return [
            'First Term' => 120,
            'Second Term' => 110,
            'Third Term' => 104,
        ][$term] ?? 100;
    }

    /**
     * Estimate attendance based on consistency
     */
    protected function estimateAttendance(int $schoolDays, int $consistencyScore): int
    {
        // Consistency 5 → 97-100% attendance
        // Consistency 4 → 92-96%
        // Consistency 3 → 85-91%
        // Consistency 2 → 78-84%
        // Consistency 1 → 70-77%
        
        $ranges = [
            5 => [97, 100],
            4 => [92, 96],
            3 => [85, 91],
            2 => [78, 84],
            1 => [70, 77],
        ];
        
        $range = $ranges[$consistencyScore] ?? [90, 95];
        $percentage = rand($range[0], $range[1]);
        
        return (int) round(($percentage / 100) * $schoolDays);
    }

    /**
     * Generate affective traits based on performance
     */
    protected function generateAffectiveTraits(array $analysis): array
    {
        $avg = $analysis['avg_percentage'];
        $consistency = $analysis['consistency_score'];
        
        // Base rating: scale from performance
        // Top students: 4-5, Average: 3-4, Struggling: 2-3
        if ($avg >= 80) {
            $base = 5;
            $variation = [4, 5];
        } elseif ($avg >= 70) {
            $base = 4;
            $variation = [4, 5];
        } elseif ($avg >= 60) {
            $base = 4;
            $variation = [3, 4, 5];
        } elseif ($avg >= 50) {
            $base = 3;
            $variation = [3, 4];
        } elseif ($avg >= 40) {
            $base = 3;
            $variation = [2, 3, 4];
        } else {
            $base = 2;
            $variation = [2, 3];
        }

        $traits = [];

        // Traits that depend heavily on consistency
        $traits['Punctuality'] = $this->pickRating($consistency, $variation);
        $traits['Willingness To Learn'] = $this->pickRating($base, $variation);
        $traits['Teamwork'] = $this->pickRating($base, $variation);
        $traits['Mental Alertness'] = $this->pickRating($base, $variation);
        
        // Traits influenced by position (top students often more confident/respectful)
        $respectBase = $analysis['is_top_performer'] ? 5 : ($base >= 4 ? 4 : 3);
        $traits['Respect'] = rand($respectBase, 5);
        $traits['Politeness'] = rand($respectBase, 5);
        
        // Traits usually fairly stable
        $traits['Neatness'] = $this->pickRating(4, [3, 4, 5]);
        $traits['Honesty'] = $this->pickRating(4, [3, 4, 5]);
        
        // Peer relationship - strong for top and middle performers, can be lower for very struggling
        if ($analysis['is_bottom_performer'] && $avg < 40) {
            $traits['Peer Relationship'] = rand(3, 4);
        } else {
            $traits['Peer Relationship'] = $this->pickRating($base, $variation);
        }

        return $traits;
    }

    /**
     * Generate psychomotor skills based on performance (with randomization)
     */
    protected function generatePsychomotorSkills(array $analysis, string $studentName): array
    {
        $avg = $analysis['avg_percentage'];
        $base = $avg >= 70 ? 4 : ($avg >= 50 ? 3 : 2);

        // Use the student's name as a seed for deterministic randomness
        // So the same student gets the same skills each time
        $seed = crc32($studentName);
        srand($seed);

        // Skills vary by "talent area" — random but seeded
        $skills = [
            'Verbal Skills' => $this->weightedRandom($base, 4),
            'Sports & Games' => $this->weightedRandom(3, 5),
            'Artistic Skills' => $this->weightedRandom(2, 4),
            'Music' => $this->weightedRandom(2, 4),
            'Dance' => $this->weightedRandom(2, 4),
            'Entrepreneurship' => $this->weightedRandom($base, 4),
            'Chess' => $this->weightedRandom(2, 4),
        ];

        // Reset seed
        srand();

        return $skills;
    }

    /**
     * Weighted random within a range, biased toward the lower end
     */
    protected function weightedRandom(int $min, int $max): int
    {
        $range = range($min, $max);
        // Weight lower values more
        $weights = [];
        $totalWeight = 0;
        foreach ($range as $i => $value) {
            $weight = count($range) - $i;
            $weights[] = $weight;
            $totalWeight += $weight;
        }

        $rand = mt_rand(1, $totalWeight);
        $cumulative = 0;
        foreach ($range as $i => $value) {
            $cumulative += $weights[$i];
            if ($rand <= $cumulative) {
                return $value;
            }
        }
        return $min;
    }

    /**
     * Pick a rating, biased toward a base value
     */
    protected function pickRating(int $base, array $variation): int
    {
        // 60% chance of base, 40% chance of variation
        if (mt_rand(1, 100) <= 60) {
            return $base;
        }
        return $variation[array_rand($variation)];
    }

    /**
     * Generate class teacher's comment
     */
    protected function generateTeacherComment(array $analysis, string $name): string
    {
        $firstName = explode(' ', trim($name))[0];
        $avg = $analysis['avg_percentage'];
        $grade = $analysis['overall_grade'];
        $position = $analysis['position'];
        $total = $analysis['total_students'];

        // Position phrase
        $posPhrase = '';
        if ($position && $total) {
            if ($position == 1) $posPhrase = 'emerged as the top student in the class';
            elseif ($position <= 3) $posPhrase = "secured the {$this->ordinal($position)} position in the class";
            elseif ($position <= ceil($total * 0.25)) $posPhrase = "performed excellently, placing {$this->ordinal($position)} in the class";
            elseif ($position <= ceil($total * 0.5)) $posPhrase = "performed above average, ranking {$this->ordinal($position)} in the class";
            else $posPhrase = "ranked {$this->ordinal($position)} in the class";
        }

        // Performance phrase
        if ($avg >= 80) {
            return "{$firstName} {$posPhrase}. A brilliant and highly disciplined student who consistently demonstrates excellence. Keep up the outstanding work!";
        } elseif ($avg >= 70) {
            return "{$firstName} {$posPhrase}. A hardworking and dedicated student with strong academic potential. Continue to give your best.";
        } elseif ($avg >= 60) {
            return "{$firstName} {$posPhrase}. Shows good understanding of the subjects. With more focus, {$firstName} can achieve even greater results.";
        } elseif ($avg >= 50) {
            return "{$firstName} {$posPhrase}. An average performance. More attention to studies will yield significant improvement.";
        } elseif ($avg >= 40) {
            return "{$firstName} {$posPhrase}. There is room for improvement. {$firstName} should be more serious with academic work and seek help where needed.";
        } else {
            return "{$firstName} {$posPhrase}. Academic performance needs significant improvement. Consistent effort and dedication are strongly advised. Parents should please assist at home.";
        }
    }

    /**
     * Generate principal's comment
     */
    protected function generatePrincipalComment(array $analysis, string $name): string
    {
        $firstName = explode(' ', trim($name))[0];
        $avg = $analysis['avg_percentage'];
        $grade = $analysis['overall_grade'];

        // Grade-based comments
        if (in_array($grade, ['A', 'A1', 'B', 'B2', 'B3'])) {
            $comments = [
                "An excellent result. {$firstName} is a source of pride to the school. Keep the flag flying.",
                "A beautiful and brilliant result. {$firstName} is a disciplined and focused student. Well done!",
                "Outstanding performance. {$firstName} has shown commitment to excellence. Keep it up.",
                "Impressive result. {$firstName} is a shining example for other students. Congratulations!",
            ];
        } elseif (in_array($grade, ['C', 'C4', 'C5', 'C6'])) {
            $comments = [
                "A good result. {$firstName} should continue working hard to achieve even better next term.",
                "A commendable performance. More effort will push {$firstName} to the top tier.",
                "Satisfactory result. {$firstName} is encouraged to aim higher.",
            ];
        } elseif (in_array($grade, ['D', 'D7', 'E', 'E8'])) {
            $comments = [
                "An average result. {$firstName} needs to be more serious and focused on academic work.",
                "There is room for improvement. {$firstName} should dedicate more time to studies.",
                "Fair performance. {$firstName} is advised to seek help from teachers and put in more effort.",
            ];
        } else {
            $comments = [
                "A poor result. {$firstName} must take academic work seriously. Parents are advised to intervene.",
                "Performance is below expectation. {$firstName} needs urgent and serious attention to studies.",
                "Result requires significant improvement. {$firstName} should be more disciplined and focused.",
            ];
        }

        return $comments[array_rand($comments)];
    }

    /**
     * Convert number to ordinal (1st, 2nd, 3rd, etc.)
     */
    protected function ordinal(int $number): string
    {
        $ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            return $number . 'th';
        }
        return $number . $ends[$number % 10];
    }
}