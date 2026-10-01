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

class ReportCardService
{
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
                ]
            );

            $reportCards[] = $reportCard;
        }

        return $reportCards;
    }

    /**
     * Calculate a student's data for report card
     */
    private function calculateStudentData($student, $term, $academicYear)
    {
        // Get all subjects for the student's class
        $subjects = Subject::where('class_id', $student->class_id)->get();
        
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

            // Calculate test score (best attempt or average)
            $testScore = 0;
            $testMax = 0;
            foreach ($tests as $test) {
                $bestAttempt = ExamAttempt::where('exam_id', $test->id)
                    ->where('student_id', $student->id)
                    ->where('status', 'submitted')
                    ->orderBy('score', 'desc')
                    ->first();
                
                $testMax += $test->max_marks;
                
                if ($bestAttempt) {
                    // Convert raw score to max_marks scale
                    $scaledScore = $test->convertScoreToMaxMarks($bestAttempt->score);
                    $testScore += $scaledScore;
                }
            }

            // Calculate exam score
            $examScore = 0;
            $examMax = 0;
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
                }
            }

            $subjectTotal = $testScore + $examScore;
            $subjectMax = $testMax + $examMax;
            $subjectPercentage = $subjectMax > 0 ? ($subjectTotal / $subjectMax) * 100 : 0;

            $subjectScores[] = [
                'subject' => $subject->name,
                'subject_id' => $subject->id,
                'test_score' => round($testScore, 2),
                'test_max' => $testMax,
                'exam_score' => round($examScore, 2),
                'exam_max' => $examMax,
                'total' => round($subjectTotal, 2),
                'max' => $subjectMax,
                'percentage' => round($subjectPercentage, 2),
                'grade' => $this->calculateGrade($subjectPercentage),
                'remark' => $this->calculateRemarks($subjectPercentage),
            ];

            $subjectBreakdown[] = [
                'subject_id' => $subject->id,
                'subject_name' => $subject->name,
                'tests' => $tests->map(function($test) use ($student) {
                    $attempt = ExamAttempt::where('exam_id', $test->id)
                        ->where('student_id', $student->id)
                        ->where('status', 'submitted')
                        ->orderBy('score', 'desc')
                        ->first();
                    
                    return [
                        'id' => $test->id,
                        'title' => $test->title,
                        'max_marks' => $test->max_marks,
                        'attempted' => $attempt ? true : false,
                        'raw_score' => $attempt ? $attempt->score : 0,
                        'raw_total' => $test->total_score,
                        'scaled_score' => $attempt ? $test->convertScoreToMaxMarks($attempt->score) : 0,
                    ];
                })->toArray(),
                'exams' => $exams->map(function($exam) use ($student) {
                    $attempt = ExamAttempt::where('exam_id', $exam->id)
                        ->where('student_id', $student->id)
                        ->where('status', 'submitted')
                        ->orderBy('score', 'desc')
                        ->first();
                    
                    return [
                        'id' => $exam->id,
                        'title' => $exam->title,
                        'max_marks' => $exam->max_marks,
                        'attempted' => $attempt ? true : false,
                        'raw_score' => $attempt ? $attempt->score : 0,
                        'raw_total' => $exam->total_score,
                        'scaled_score' => $attempt ? $exam->convertScoreToMaxMarks($attempt->score) : 0,
                    ];
                })->toArray(),
            ];

            $totalTestScore += $testScore;
            $totalTestMax += $testMax;
            $totalExamScore += $examScore;
            $totalExamMax += $examMax;
        }

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
}