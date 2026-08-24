<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Student;
use App\Models\Result;
use App\Models\ReportCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function dashboard()
    {
        $student = Auth::user()->student;
        
        $availableExams = Exam::where('status', 'active')
                             ->where('start_date', '<=', now())
                             ->where('end_date', '>=', now())
                             ->whereHas('subject', function($query) use ($student) {
                                 $query->where('class_id', $student->class_id);
                             })
                             ->count();
        
        $completedExams = ExamAttempt::where('student_id', $student->id)
                                    ->where('status', 'submitted')
                                    ->count();
        
        $averageScore = Result::where('student_id', $student->id)
                             ->avg('percentage') ?? 0;
        
        $recentResults = Result::where('student_id', $student->id)
                              ->with(['subject', 'exam'])
                              ->latest()
                              ->limit(5)
                              ->get();
        
        return view('student.dashboard', compact(
            'availableExams', 'completedExams', 'averageScore', 'recentResults'
        ));
    }

  // app/Http/Controllers/Student/StudentController.php
// Update the exams method

public function exams()
{
    $student = Auth::user()->student;
    
    // Get available exams (published, active, and within date range)
    $availableExams = Exam::where('is_published', true)
                         ->where('status', 'active')
                         ->where('start_date', '<=', now())
                         ->where('end_date', '>=', now())
                         ->whereHas('subject', function($query) use ($student) {
                             $query->where('class_id', $student->class_id);
                         })
                         ->with(['subject', 'attempts' => function($query) use ($student) {
                             $query->where('student_id', $student->id);
                         }])
                         ->orderBy('start_date')
                         ->get();
    
    // Get upcoming exams
    $upcomingExams = Exam::where('is_published', true)
                        ->where('status', 'upcoming')
                        ->where('start_date', '>', now())
                        ->whereHas('subject', function($query) use ($student) {
                            $query->where('class_id', $student->class_id);
                        })
                        ->with(['subject'])
                        ->orderBy('start_date')
                        ->get();
    
    // Get completed exams
    $completedExams = ExamAttempt::where('student_id', $student->id)
                                ->where('status', 'submitted')
                                ->with(['exam', 'exam.subject'])
                                ->latest()
                                ->paginate(10);
    
    return view('student.exams', compact('availableExams', 'upcomingExams', 'completedExams'));
}

public function takeExam($examId)
{
    $student = Auth::user()->student;
    $exam = Exam::with(['questions' => function($query) {
        $query->inRandomOrder();
    }])->findOrFail($examId);
    
    // Check if student can take the exam
    $canTake = $exam->canStudentTake($student->id);
    
    if (!$canTake['can']) {
        return redirect()->route('student.exams')
            ->with('error', $canTake['reason']);
    }
    
    // If there's an in-progress attempt, continue it
    if (isset($canTake['attempt']) && $canTake['attempt']) {
        return redirect()->route('student.exam.continue', $canTake['attempt']->id);
    }
    
    // Create new attempt
    $attempt = ExamAttempt::create([
        'exam_id' => $examId,
        'student_id' => $student->id,
        'started_at' => now(),
        'status' => 'in_progress',
    ]);
    
    return redirect()->route('student.exam.continue', $attempt->id);
}

    public function continueExam($attemptId)
    {
        $attempt = ExamAttempt::with(['exam.questions' => function($query) {
            $query->orderBy('exam_questions.question_order');
        }])->findOrFail($attemptId);
        
        $student = Auth::user()->student;
        
        // Verify ownership
        if ($attempt->student_id !== $student->id) {
            abort(403);
        }
        
        // Check if exam is still active
        if (!$attempt->exam->isActive()) {
            return redirect()->route('student.exams')
                ->with('error', 'This exam has expired.');
        }
        
        // Check if attempt is still in progress
        if ($attempt->status !== 'in_progress') {
            return redirect()->route('student.exams')
                ->with('error', 'This exam attempt has already been completed.');
        }
        
        // Check time limit
        $timeRemaining = $attempt->time_remaining;
        if ($timeRemaining <= 0) {
            return $this->submitExam($attemptId);
        }
        
        $questions = $attempt->exam->questions;
        $answers = $attempt->answers ?? [];
        
        return view('student.take-exam', compact('attempt', 'questions', 'answers', 'timeRemaining'));
    }

    public function submitExam($attemptId)
    {
        $attempt = ExamAttempt::with(['exam.questions'])->findOrFail($attemptId);
        $student = Auth::user()->student;
        
        if ($attempt->student_id !== $student->id) {
            abort(403);
        }
        
        if ($attempt->status === 'submitted') {
            return redirect()->route('student.exams')
                ->with('info', 'This exam has already been submitted.');
        }
        
        $answers = $attempt->answers ?? [];
        $score = 0;
        $totalQuestions = $attempt->exam->questions->count();
        $answeredQuestions = count($answers);
        
        // Calculate score
        foreach ($attempt->exam->questions as $question) {
            if (isset($answers[$question->id]) && $answers[$question->id] === $question->correct_answer) {
                $score += $question->score;
            }
        }
        
        $attempt->update([
            'completed_at' => now(),
            'score' => $score,
            'total_questions_answered' => $answeredQuestions,
            'status' => 'submitted',
        ]);
        
        // Create result
        $percentage = ($score / $attempt->exam->total_score) * 100;
        
        Result::create([
            'student_id' => $student->id,
            'subject_id' => $attempt->exam->subject_id,
            'exam_id' => $attempt->exam->id,
            'assessment_type' => 'CBT Exam',
            'score' => $score,
            'max_score' => $attempt->exam->total_score,
            'percentage' => $percentage,
            'assessment_date' => now(),
        ]);
        
        return redirect()->route('student.exam.result', $attempt->id)
            ->with('success', 'Exam submitted successfully!');
    }

    public function examResult($attemptId)
    {
        $attempt = ExamAttempt::with(['exam'])->findOrFail($attemptId);
        $student = Auth::user()->student;
        
        if ($attempt->student_id !== $student->id) {
            abort(403);
        }
        
        $result = Result::where('exam_id', $attempt->exam_id)
                       ->where('student_id', $student->id)
                       ->first();
        
        return view('student.exam-result', compact('attempt', 'result'));
    }

    public function results()
    {
        $student = Auth::user()->student;
        $results = Result::where('student_id', $student->id)
                        ->with(['subject', 'exam'])
                        ->latest()
                        ->paginate(15);
        
        return view('student.results', compact('results'));
    }

    public function reportCards()
    {
        $student = Auth::user()->student;
        $reportCards = ReportCard::where('student_id', $student->id)
                                ->orderBy('academic_year', 'desc')
                                ->orderBy('term', 'desc')
                                ->get();
        
        return view('student.report-cards', compact('reportCards'));
    }

    public function viewReportCard($id)
    {
        $reportCard = ReportCard::with(['student', 'class'])
                               ->findOrFail($id);
        
        $student = Auth::user()->student;
        
        if ($reportCard->student_id !== $student->id) {
            abort(403);
        }
        
        return view('student.view-report-card', compact('reportCard'));
    }

    public function saveAnswer(Request $request, $attemptId)
    {
        $attempt = ExamAttempt::findOrFail($attemptId);
        $student = Auth::user()->student;
        
        if ($attempt->student_id !== $student->id) {
            abort(403);
        }
        
        if ($attempt->status !== 'in_progress') {
            return response()->json(['error' => 'Exam already submitted'], 400);
        }
        
        $answers = $attempt->answers ?? [];
        $answers[$request->question_id] = $request->answer;
        
        $attempt->update(['answers' => $answers]);
        
        return response()->json(['success' => true]);
    }
}