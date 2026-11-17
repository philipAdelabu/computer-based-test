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
    // Get subjects assigned to the student
    $subjects = $student->subjects()->with(['teacher'])->orderBy('name')->get();

    $subjectScores = [];
    $subjectBreakdown = [];
    $totalTestScore = 0;
    $totalTestMax = 0;
    $totalExamScore = 0;
    $totalExamMax = 0;

    // Fetch Admin Weights
    $caWeight = (float) \App\Models\Setting::get('ca_weight', 40);
    $examWeight = (float) \App\Models\Setting::get('exam_weight', 60);

    // Define the term order for snapshots
    $termOrder = ['First Term', 'Second Term', 'Third Term'];

    foreach ($subjects as $subject) {
        
        // ============ FETCH ASSESSMENTS FOR CURRENT TERM ============
        $tests = Exam::where('subject_id', $subject->id)
            ->where('assessment_type', 'test')
            ->where('term', $term)
            ->where('academic_year', $academicYear)
            ->where('is_published', true)
            ->get();

        $exams = Exam::where('subject_id', $subject->id)
            ->where('assessment_type', 'exam')
            ->where('term', $term)
            ->where('academic_year', $academicYear)
            ->where('is_published', true)
            ->get();

        // ============ CALCULATE CURRENT TERM SCORES ============
        $testScore = 0;
        $testMax = 0;
        $testAttempted = false;
        $testAttempts = [];

        foreach ($tests as $test) {
            $manualResult = Result::where('exam_id', $test->id)->where('student_id', $student->id)->first();
            $testMax += $test->max_marks;

            if ($manualResult) {
                $scaled = $test->convertScoreToMaxMarks($manualResult->score);
                $testScore += $scaled;
                $testAttempted = true;
                $testAttempts[] = ['id' => $test->id, 'title' => $test->title, 'attempted' => true, 'raw_score' => $manualResult->score, 'raw_total' => $test->total_score, 'scaled_score' => round($scaled, 2)];
            } else {
                $bestAttempt = ExamAttempt::where('exam_id', $test->id)->where('student_id', $student->id)->where('status', 'submitted')->orderBy('score', 'desc')->first();
                if ($bestAttempt) {
                    $scaled = $test->convertScoreToMaxMarks($bestAttempt->score);
                    $testScore += $scaled;
                    $testAttempted = true;
                    $testAttempts[] = ['id' => $test->id, 'title' => $test->title, 'attempted' => true, 'raw_score' => $bestAttempt->score, 'raw_total' => $test->total_score, 'scaled_score' => round($scaled, 2)];
                } else {
                    $testAttempts[] = ['id' => $test->id, 'title' => $test->title, 'attempted' => false, 'raw_score' => null, 'raw_total' => $test->total_score, 'scaled_score' => null];
                }
            }
        }

        $examScore = 0;
        $examMax = 0;
        $examAttempted = false;
        $examAttempts = [];

        foreach ($exams as $exam) {
            $manualResult = Result::where('exam_id', $exam->id)->where('student_id', $student->id)->first();
            $examMax += $exam->max_marks;

            if ($manualResult) {
                $scaled = $exam->convertScoreToMaxMarks($manualResult->score);
                $examScore += $scaled;
                $examAttempted = true;
                $examAttempts[] = ['id' => $exam->id, 'title' => $exam->title, 'attempted' => true, 'raw_score' => $manualResult->score, 'raw_total' => $exam->total_score, 'scaled_score' => round($scaled, 2)];
            } else {
                $bestAttempt = ExamAttempt::where('exam_id', $exam->id)->where('student_id', $student->id)->where('status', 'submitted')->orderBy('score', 'desc')->first();
                if ($bestAttempt) {
                    $scaled = $exam->convertScoreToMaxMarks($bestAttempt->score);
                    $examScore += $scaled;
                    $examAttempted = true;
                    $examAttempts[] = ['id' => $exam->id, 'title' => $exam->title, 'attempted' => true, 'raw_score' => $bestAttempt->score, 'raw_total' => $exam->total_score, 'scaled_score' => round($scaled, 2)];
                } else {
                    $examAttempts[] = ['id' => $exam->id, 'title' => $exam->title, 'attempted' => false, 'raw_score' => null, 'raw_total' => $exam->total_score, 'scaled_score' => null];
                }
            }
        }

        // ============ CALCULATE FINAL WEIGHTED SCORES ============
        // 1. Calculate percentages
        $testPercentage = $testMax > 0 ? ($testScore / $testMax) * 100 : 0;
        $examPercentage = $examMax > 0 ? ($examScore / $examMax) * 100 : 0;

        // 2. Apply weights (e.g., 30% to CA, 70% to Exam)
        $weightedTestScore = ($testPercentage / 100) * $caWeight;
        $weightedExamScore = ($examPercentage / 100) * $examWeight;

        // 3. Final subject total out of 100
        $subjectTotal = round($weightedTestScore + $weightedExamScore, 1);
        $subjectMax = 100;
        
        $anyAttempt = $testAttempted || $examAttempted;
        $subjectPercentage = $anyAttempt ? $subjectTotal : null;
        $grade = $anyAttempt ? $this->calculateGrade($subjectPercentage) : null;
        $remark = $anyAttempt ? $this->calculateRemarks($subjectPercentage) : null;

        // Handle manual assignment/project scores
        $manualResults = \App\Models\Result::where('student_id', $student->id)
            ->where('subject_id', $subject->id)
            ->where('term', $term)
            ->whereIn('assessment_type', ['assignment', 'project', 'quiz'])
            ->get();

        // If manual results exist, you can add them to $weightedTestScore here if needed
        // (Assuming you aren't mixing them with CBT tests for now)

          // ============ FETCH PREVIOUS TERMS (IMMUTABLE SNAPSHOTS) ============
              // ============ FETCH PREVIOUS TERMS (IMMUTABLE SNAPSHOTS) ============
                $termOrder = ['First Term', 'Second Term', 'Third Term'];
                $firstTermData = null;
                $secondTermData = null;
                $thirdTermData = null;

                foreach ($termOrder as $t) {
                    if ($t === $term) {
                        // Save the CURRENT term's exact snapshot
                        $currentData = [
                            'ca' => $anyAttempt ? round($weightedTestScore, 1) : null,
                            'exam' => $anyAttempt ? round($weightedExamScore, 1) : null,
                            'total' => $anyAttempt ? $subjectTotal : null,
                            'grade' => $grade,
                        ];
                        if ($t === 'First Term') $firstTermData = $currentData;
                        if ($t === 'Second Term') $secondTermData = $currentData;
                        if ($t === 'Third Term') $thirdTermData = $currentData;
                        break; 
                    }

                    $key = '';
                    if ($t === 'First Term') $key = 'first';
                    if ($t === 'Second Term') $key = 'second';
                    if ($t === 'Third Term') $key = 'third';

                    $prevReport = ReportCard::where('student_id', $student->id)
                        ->where('term', $t)
                        ->where('academic_year', $academicYear)
                        ->first();

                    if ($prevReport && $prevReport->subject_scores) {
                        foreach ($prevReport->subject_scores as $prevScore) {
                            if (($prevScore['subject_id'] ?? null) === $subject->id) {
                                $snapshot = $prevScore['term_scores'][$key] ?? null;
                                
                                if ($t === 'First Term') $firstTermData = $snapshot;
                                if ($t === 'Second Term') $secondTermData = $snapshot;
                                if ($t === 'Third Term') $thirdTermData = $snapshot;
                                break;
                            }
                        }
                    }
                }

        // ============ CALCULATE SESSION AVERAGE ============
        // Average only the terms that have a valid total
        $availableTotals = array_filter([
            $firstTermData['total'] ?? null,
            $secondTermData['total'] ?? null,
            $thirdTermData['total'] ?? null,
        ]);
        
        $sessionAvg = count($availableTotals) > 0 ? round(array_sum($availableTotals) / count($availableTotals), 1) : null;

        // ============ BUILD SUBJECT SCORE ARRAY ============
        $subjectScores[] = [
            'subject' => $subject->name,
            'subject_id' => $subject->id,
            'subject_code' => $subject->code,
            'teacher' => $subject->teacher->name ?? null,
            'test_score' => $testAttempted ? round($weightedTestScore, 2) : null, // Saving weighted score
            'test_max' => $caWeight,
            'test_attempted' => $testAttempted,
            'exam_score' => $examAttempted ? round($weightedExamScore, 2) : null, // Saving weighted score
            'exam_max' => $examWeight,
            'exam_attempted' => $examAttempted,
            'total' => $anyAttempt ? $subjectTotal : null,
            'max' => 100,
            'percentage' => $subjectPercentage,
            'grade' => $grade,
            'remark' => $remark,
            'attempted' => $anyAttempt,
            'has_assessments' => ($tests->count() > 0 || $exams->count() > 0),
            'term_scores' => [
                'first' => $firstTermData,
                'second' => $secondTermData,
                'third' => $thirdTermData,
            ],
            'session_avg' => $sessionAvg,
        ];

        $subjectBreakdown[] = [
            'subject_id' => $subject->id,
            'subject_name' => $subject->name,
            'tests' => $testAttempts,
            'exams' => $examAttempts,
        ];

        // ============ ADD TO GRAND TOTALS ============
        if ($anyAttempt) {
            $totalTestScore += $weightedTestScore;
            $totalTestMax += $caWeight;
            $totalExamScore += $weightedExamScore;
            $totalExamMax += $examWeight;
        } else {
            $totalTestMax += $caWeight;
            $totalExamMax += $examWeight;
        }
    }

    // ============ RETURN DATA ============
    $grandTotal = $totalTestScore + $totalExamScore;
    $grandMax = $totalTestMax + $totalExamMax;
    $averagePercentage = $grandMax > 0 ? ($grandTotal / $grandMax) * 100 : 0;

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