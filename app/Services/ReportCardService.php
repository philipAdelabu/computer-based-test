<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Result;
use App\Models\ReportCard;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\GradeService;
use App\Services\ReportCardAutoFillService;

class ReportCardService
{

     protected $gradeService;
    protected $autoFillService;

    public function __construct()
    {
        $this->gradeService = new GradeService();
        $this->autoFillService = new ReportCardAutoFillService();
    }

    /**
     * Generate report cards for all students in a class for a given term/year
     */
    public function generateForClass($classId, $term, $academicYear)
    {
        $students = Student::where('class_id', $classId)
                          ->where('status', 'active')
                          ->with('user')
                          ->get();

        $reportCards = [];
        $allTotals = [];

        // First pass: calculate all students' totals (for ranking)
        foreach ($students as $student) {
            $data = $this->calculateStudentData($student, $term, $academicYear);
            $allTotals[$student->id] = $data['grand_total'];
        }

        // Sort for ranking
        arsort($allTotals);
        $rankings = [];
        $rank = 1;
        $previousTotal = null;
        $actualRank = 1;
        
        foreach ($allTotals as $studentId => $total) {
            if ($previousTotal !== null && $total < $previousTotal) {
                $rank = $actualRank;
            }
            $rankings[$studentId] = $rank;
            $previousTotal = $total;
            $actualRank++;
        }

        // Second pass: create/update report cards
        foreach ($students as $student) {
            $data = $this->calculateStudentData($student, $term, $academicYear);
            
            $reportCard = ReportCard::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'class_id' => $classId,
                    'term' => $term,
                    'academic_year' => $academicYear,
                ],
                [
                    'subject_scores' => $data['subject_scores'],
                    'subject_breakdown' => $data['subject_breakdown'],
                    'total_score' => $data['grand_total'],
                    'average_score' => $data['average_percentage'],
                    'grade' => $this->calculateGrade($data['average_percentage']),
                    'remarks' => $this->calculateRemarks($data['average_percentage']),
                    'position' => $rankings[$student->id] ?? null,
                    'total_students' => $students->count(),
                    'total_test_score' => $data['total_test_score'],
                    'total_test_max' => $data['total_test_max'],
                    'total_exam_score' => $data['total_exam_score'],
                    'total_exam_max' => $data['total_exam_max'],
                    'grand_total' => $data['grand_total'],
                    'grand_max' => $data['grand_max'],
                    'generated_date' => now(),
                    'generated_by' => Auth::id(),
                     // NEW auto-fill
                     'gender' => $student->gender, 
                    'session' => $academicYear . '/' . ($academicYear + 1),
                    'age' => $student->date_of_birth ? $student->date_of_birth->age : null,
                    'no_in_class' => $students->count(),
                    'marks_obtainable' => $data['grand_max'],
                    'times_school_opened' => $this->getSchoolOpenDays($term, $academicYear),
                    'times_present' => null, // Admin fills
                ]
            );
            $reportCards[] = $reportCard;
        }
         
          foreach ($reportCards as $reportCard) {
            try {
                // Only auto-fill fields that are empty
                $this->autoFillService->autoFill($reportCard, false);
            } catch (\Exception $e) {
                \Log::error("Auto-fill failed for report card {$reportCard->id}: " . $e->getMessage());
            }
        }

        return $reportCards;
    }

    /**
     * Calculate a student's data for report card
     */
    // app/Services/ReportCardService.php
// Replace the calculateStudentData method entirely

private function calculateStudentData($student, $term, $academicYear)
{
    // Get ALL subjects for the student's class
    // (this is the KEY change - not just subjects with attempts)
    $subjects = Subject::where('class_id', $student->class_id)
                       ->with(['teacher'])
                       ->orderBy('name')
                       ->get();

    $subjectScores = [];
    $subjectBreakdown = [];
    $totalTestScore = 0;
    $totalTestMax = 0;
    $totalExamScore = 0;
    $totalExamMax = 0;

    foreach ($subjects as $subject) {
        // Get all tests for this subject in the term
        $tests = Exam::where('subject_id', $subject->id)
            ->where('assessment_type', 'test')
            ->where('term', $term)
            ->where('academic_year', $academicYear)
            ->where('is_published', true)
            ->get();

        // Get all exams for this subject in the term
        $exams = Exam::where('subject_id', $subject->id)
            ->where('assessment_type', 'exam')
            ->where('term', $term)
            ->where('academic_year', $academicYear)
            ->where('is_published', true)
            ->get();

        // ============ CALCULATE TEST SCORE ============
        $testScore = 0;
        $testMax = 0;
        $testAttempted = false;
        $testAttempts = [];

        foreach ($tests as $test) {
            $bestAttempt = ExamAttempt::where('exam_id', $test->id)
                ->where('student_id', $student->id)
                ->where('status', 'submitted')
                ->orderBy('score', 'desc')
                ->first();

            $testMax += $test->max_marks;

            if ($bestAttempt) {
                $scaledScore = $test->convertScoreToMaxMarks($bestAttempt->score);
                $testScore += $scaledScore;
                $testAttempted = true;

                $testAttempts[] = [
                    'id' => $test->id,
                    'title' => $test->title,
                    'max_marks' => $test->max_marks,
                    'attempted' => true,
                    'raw_score' => $bestAttempt->score,
                    'raw_total' => $test->total_score,
                    'scaled_score' => round($scaledScore, 2),
                ];
            } else {
                $testAttempts[] = [
                    'id' => $test->id,
                    'title' => $test->title,
                    'max_marks' => $test->max_marks,
                    'attempted' => false,
                    'raw_score' => null,
                    'raw_total' => $test->total_score,
                    'scaled_score' => null,
                ];
            }
        }

        // ============ CALCULATE EXAM SCORE ============
        $examScore = 0;
        $examMax = 0;
        $examAttempted = false;
        $examAttempts = [];

        foreach ($exams as $exam) {
            $bestAttempt = ExamAttempt::where('exam_id', $exam->id)
                ->where('student_id', $student->id)
                ->where('status', 'submitted')
                ->orderBy('score', 'desc')
                ->first();

            $examMax += $exam->max_marks;

            if ($bestAttempt) {
                $scaledScore = $exam->convertScoreToMaxMarks($bestAttempt->score);
                $examScore += $scaledScore;
                $examAttempted = true;

                $examAttempts[] = [
                    'id' => $exam->id,
                    'title' => $exam->title,
                    'max_marks' => $exam->max_marks,
                    'attempted' => true,
                    'raw_score' => $bestAttempt->score,
                    'raw_total' => $exam->total_score,
                    'scaled_score' => round($scaledScore, 2),
                ];
            } else {
                $examAttempts[] = [
                    'id' => $exam->id,
                    'title' => $exam->title,
                    'max_marks' => $exam->max_marks,
                    'attempted' => false,
                    'raw_score' => null,
                    'raw_total' => $exam->total_score,
                    'scaled_score' => null,
                ];
            }
        }

        // ============ SUBJECT TOTALS ============
        $subjectTotal = $testScore + $examScore;
        $subjectMax = $testMax + $examMax;

        // If no assessments were even created, mark subject as "not assessed"
        $hasAnyAssessments = ($tests->count() > 0 || $exams->count() > 0);
        $anyAttempt = $testAttempted || $examAttempted;

        if (!$hasAnyAssessments || !$anyAttempt) {
            // Student has not attempted ANY assessment for this subject
            $subjectPercentage = null;
            $grade = null;
            $remark = null;
        } else {
            $subjectPercentage = $subjectMax > 0 
                ? round(($subjectTotal / $subjectMax) * 100, 2) 
                : 0;
            $grade = $this->calculateGrade($subjectPercentage);
            $remark = $this->calculateRemarks($subjectPercentage);
        }

        $subjectScores[] = [
            'subject' => $subject->name,
            'subject_id' => $subject->id,
            'subject_code' => $subject->code,
            'teacher' => $subject->teacher->name ?? null,
            'test_score' => $testAttempted ? round($testScore, 2) : null,
            'test_max' => $testMax,
            'test_attempted' => $testAttempted,
            'exam_score' => $examAttempted ? round($examScore, 2) : null,
            'exam_max' => $examMax,
            'exam_attempted' => $examAttempted,
            'total' => $anyAttempt ? round($subjectTotal, 2) : null,
            'max' => $subjectMax,
            'percentage' => $subjectPercentage,
            'grade' => $grade,
            'remark' => $remark,
            'attempted' => $anyAttempt,
            'has_assessments' => $hasAnyAssessments,
        ];

        $subjectBreakdown[] = [
            'subject_id' => $subject->id,
            'subject_name' => $subject->name,
            'tests' => $testAttempts,
            'exams' => $examAttempts,
        ];

        // Only add to totals if attempted
        if ($anyAttempt) {
            $totalTestScore += $testScore;
            $totalTestMax += $testMax;
            $totalExamScore += $examScore;
            $totalExamMax += $examMax;
        } else {
            // Still count max marks so percentages work correctly
            $totalTestMax += $testMax;
            $totalExamMax += $examMax;
        }
    }

    $grandTotal = $totalTestScore + $totalExamScore;
    $grandMax = $totalTestMax + $totalExamMax;
    $averagePercentage = $grandMax > 0 ? ($grandTotal / $grandMax) * 100 : 0;
    
     $previousTerms = $this->getPreviousTerms($term);
        $previousTermScores = [];

        foreach ($previousTerms as $prevTerm) {
            $prevReport = ReportCard::where('student_id', $student->id)
                ->where('class_id', $student->class_id)
                ->where('term', $prevTerm)
                ->where('academic_year', $academicYear)
                ->first();

            if ($prevReport && $prevReport->subject_scores) {
                foreach ($prevReport->subject_scores as $prevScore) {
                    if (($prevScore['subject_id'] ?? null) === $subject->id) {
                        $previousTermScores[$prevTerm] = $prevScore['percentage'] ?? null;
                        break;
                    }
                }
            }
        }

        // Compute session average across terms
        $allTermPercentages = array_filter([
            $previousTermScores['First Term'] ?? null,
            $previousTermScores['Second Term'] ?? null,
            $previousTermScores['Third Term'] ?? null,
            $subjectPercentage,
        ]);

        $sessionAvg = !empty($allTermPercentages)
            ? round(array_sum($allTermPercentages) / count($allTermPercentages), 2)
            : null;

        // Add to subject score array:
        $subjectScores[count($subjectScores) - 1]['term_scores'] = [
            'first' => $previousTermScores['First Term'] ?? null,
            'second' => $previousTermScores['Second Term'] ?? null,
            'third' => $previousTermScores['Third Term'] ?? null,
        ];
        
        $subjectScores[count($subjectScores) - 1]['session_avg'] = $sessionAvg;


    return [
        'subject_scores' => $subjectScores,
        'subject_breakdown' => $subjectBreakdown,
        'total_test_score' => round($totalTestScore, 2),
        'total_test_max' => $totalTestMax,
        'total_exam_score' => round($totalExamScore, 2),
        'total_exam_max' => $totalExamMax,
        'grand_total' => round($grandTotal, 2),
        'grand_max' => $grandMax,
        'average_percentage' => round($averagePercentage, 2),
    ];
}

   private function getPreviousTerms($currentTerm)
    {
        $termOrder = ['First Term', 'Second Term', 'Third Term'];
        $currentIndex = array_search($currentTerm, $termOrder);
        
        if ($currentIndex === false || $currentIndex === 0) {
            return [];
        }
        
        return array_slice($termOrder, 0, $currentIndex);
    }

    private function calculateGrade($percentage)
    {
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B';
        if ($percentage >= 60) return 'C';
        if ($percentage >= 50) return 'D';
        if ($percentage >= 40) return 'E';
        return 'F';
    }

    private function calculateRemarks($percentage)
    {
        if ($percentage >= 80) return 'Excellent';
        if ($percentage >= 70) return 'Very Good';
        if ($percentage >= 60) return 'Good';
        if ($percentage >= 50) return 'Fair';
        if ($percentage >= 40) return 'Poor';
        return 'Very Poor';
    }

    
        public function getGradeAttribute()
        {
            $service = new GradeService();
            return $service->getGrade($this->percentage);
        }

        public function getRemarksAttribute()
        {
            $service = new GradeService();
            return $service->getRemark($this->percentage);
        }

        public function getGradeColorAttribute()
        {
            $service = new GradeService();
            return $service->getColor($this->percentage);
        }

        private function getSchoolOpenDays($term, $year)
        {
            // Default school days per term
            return [
                'First Term' => 120,
                'Second Term' => 110,
                'Third Term' => 104,
            ][$term] ?? 100;
        }


}