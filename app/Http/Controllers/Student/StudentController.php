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
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

class StudentController extends Controller
{
    public function __construct()
    {
      //  $this->middleware('auth');
        
        // Share available exams count with all student views
        View::composer('student.*', function ($view) {
            if (Auth::check() && Auth::user()->isStudent()) {
                $student = Auth::user()->student;
                $availableExamsCount = Exam::where('is_published', true)
                    ->where('status', 'active')
                    ->whereHas('subject', function($query) use ($student) {
                        $query->where('class_id', $student->class_id);
                    })
                    ->where(function($query) {
                        $now = Carbon::now(config('app.timezone'));
                        $query->where('schedule_type', 'no_date')
                            ->orWhere(function($q) use ($now) {
                                $q->where('schedule_type', 'single_date')
                                    ->where('start_date', '<=', $now)
                                    ->where(function($sub) use ($now) {
                                        $sub->whereNull('end_date')
                                            ->orWhere('end_date', '>=', $now);
                                    });
                            })
                            ->orWhere(function($q) use ($now) {
                                $q->where('schedule_type', 'date_range')
                                    ->where('available_from', '<=', $now)
                                    ->where('available_to', '>=', $now);
                            });
                    })
                    ->count();
                
                $view->with('availableExamsCount', $availableExamsCount);
            }
        });
    }

    public function dashboard()
    {
        $student = Auth::user()->student;
        $class = $student->class;
        $subjects = $class ? $class->subjects : collect();
        
        $availableExamsList = Exam::where('is_published', true)
            ->where('status', 'active')
            ->whereHas('subject', function($query) use ($student) {
                $query->where('class_id', $student->class_id);
            })
            ->where(function($query) {
                $now = Carbon::now(config('app.timezone'));
                $query->where('schedule_type', 'no_date')
                    ->orWhere(function($q) use ($now) {
                        $q->where('schedule_type', 'single_date')
                            ->where('start_date', '<=', $now)
                            ->where(function($sub) use ($now) {
                                $sub->whereNull('end_date')
                                    ->orWhere('end_date', '>=', $now);
                            });
                    })
                    ->orWhere(function($q) use ($now) {
                        $q->where('schedule_type', 'date_range')
                            ->where('available_from', '<=', $now)
                            ->where('available_to', '>=', $now);
                    });
            })
            ->with(['subject', 'attempts' => function($query) use ($student) {
                $query->where('student_id', $student->id);
            }])
            ->orderBy('start_date')
            ->limit(6)
            ->get();
        
        $upcomingExamsList = Exam::where('is_published', true)
            ->where('status', 'active')
            ->whereHas('subject', function($query) use ($student) {
                $query->where('class_id', $student->class_id);
            })
            ->where(function($query) {
                $now = Carbon::now(config('app.timezone'));
                $query->where(function($q) use ($now) {
                    $q->where('schedule_type', 'single_date')
                        ->where('start_date', '>', $now);
                })->orWhere(function($q) use ($now) {
                    $q->where('schedule_type', 'date_range')
                        ->where('available_from', '>', $now);
                });
            })
            ->with('subject')
            ->orderBy('start_date')
            ->limit(4)
            ->get();
        
        $availableExams = $availableExamsList->count();
        $upcomingExams = $upcomingExamsList->count();
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
            'student',
            'class',
            'subjects',
            'availableExamsList',
            'upcomingExamsList',
            'availableExams',
            'upcomingExams',
            'completedExams',
            'averageScore',
            'recentResults'
        ));
    }

    public function exams()
    {
        $student = Auth::user()->student;
        
        $availableExams = Exam::where('is_published', true)
            ->where('status', 'active')
            ->whereHas('subject', function($query) use ($student) {
                $query->where('class_id', $student->class_id);
            })
            ->where(function($query) {
                $now = Carbon::now(config('app.timezone'));
                $query->where('schedule_type', 'no_date')
                    ->orWhere(function($q) use ($now) {
                        $q->where('schedule_type', 'single_date')
                            ->where('start_date', '<=', $now)
                            ->where(function($sub) use ($now) {
                                $sub->whereNull('end_date')
                                    ->orWhere('end_date', '>=', $now);
                            });
                    })
                    ->orWhere(function($q) use ($now) {
                        $q->where('schedule_type', 'date_range')
                            ->where('available_from', '<=', $now)
                            ->where('available_to', '>=', $now);
                    });
            })
            ->with(['subject', 'attempts' => function($query) use ($student) {
                $query->where('student_id', $student->id);
            }])
            ->orderBy('start_date')
            ->get();
        
        $upcomingExams = Exam::where('is_published', true)
            ->where('status', 'active')
            ->whereHas('subject', function($query) use ($student) {
                $query->where('class_id', $student->class_id);
            })
            ->where(function($query) {
                $now = Carbon::now(config('app.timezone'));
                $query->where(function($q) use ($now) {
                    $q->where('schedule_type', 'single_date')
                        ->where('start_date', '>', $now);
                })->orWhere(function($q) use ($now) {
                    $q->where('schedule_type', 'date_range')
                        ->where('available_from', '>', $now);
                });
            })
            ->with(['subject'])
            ->orderBy('start_date')
            ->get();
        
        $completedExams = ExamAttempt::where('student_id', $student->id)
                                    ->where('status', 'submitted')
                                    ->with(['exam', 'exam.subject'])
                                    ->latest()
                                    ->paginate(10);
        
        return view('student.exams', compact('availableExams', 'upcomingExams', 'completedExams'));
    }

    /**
     * START EXAM - When student clicks "Start Exam" button
     */
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
        
        // Count existing attempts
        $attemptCount = ExamAttempt::where('exam_id', $examId)
                                  ->where('student_id', $student->id)
                                  ->count();

        
        
        // Create new attempt
        $attempt = ExamAttempt::create([
            'exam_id' => $examId,
            'student_id' => $student->id,
            'attempt_number' => $attemptCount + 1,
            'started_at' => now(),
            'status' => 'in_progress',
            'answers' => [], // Initialize empty answers
        ]);

    
     
        return redirect()->route('student.exam.continue', $attempt->id);
    }

    /**
     * CONTINUE EXAM - Show the exam page with questions
     */
  public function continueExam($attemptId)
    {
        $attempt = ExamAttempt::with(['exam'])->findOrFail($attemptId);
        $student = Auth::user()->student;
        
        if ($attempt->student_id !== $student->id) {
            abort(403);
        }
        
        // Load questions UNIQUELY (avoid duplicates)
        $questions = $attempt->exam->questions()
            ->distinct()
            ->orderBy('exam_questions.question_order')
            ->get();
        
        // Fallback: if somehow no questions, return with error
        if ($questions->isEmpty()) {
            return redirect()->route('student.exams')
                ->with('error', 'This exam has no questions. Please contact your teacher.');
        }
        
        if ($attempt->status === 'submitted') {
            return redirect()->route('student.exam.result', $attempt->id);
        }
        
        if (!$attempt->exam->is_available) {
            return redirect()->route('student.exams')
                ->with('error', 'This exam is no longer available.');
        }
        
        $timeRemaining = $attempt->time_remaining;
        if ($timeRemaining <= 0) {
            return $this->submitExam($attemptId);
        }
        
        $answers = $attempt->answers ?? [];
        
        // Find current question
        $currentQuestionId = request()->get('q');
        $currentQuestion = $currentQuestionId 
            ? $questions->firstWhere('id', $currentQuestionId)
            : null;
        
        if (!$currentQuestion) {
            $currentQuestion = $questions->first();
        }
        
        $totalQuestions = $questions->count();
        $answeredCount = count($answers);
        
        return view('student.take-exam', compact(
            'attempt', 'questions', 'answers', 
            'currentQuestion', 'timeRemaining', 
            'totalQuestions', 'answeredCount'
        ));
    }

    /**
     * SAVE ANSWER - Save answer via AJAX
     */
    public function saveAnswer(Request $request, $attemptId)
    {
        $attempt = ExamAttempt::findOrFail($attemptId);
        $student = Auth::user()->student;
        
        if ($attempt->student_id !== $student->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        if ($attempt->status !== 'in_progress') {
            return response()->json(['error' => 'Exam already submitted'], 400);
        }
        
        $answers = $attempt->answers ?? [];
        $answers[$request->question_id] = $request->answer;
        
        $attempt->update([
            'answers' => $answers,
            'total_questions_answered' => count($answers),
        ]);
        
        return response()->json(['success' => true]);
    }

    /**
     * SUBMIT EXAM - When student clicks submit or time runs out
     */
    // app/Http/Controllers/Student/StudentController.php
public function submitExam($attemptId)
{
    $attempt = ExamAttempt::with(['exam.questions'])->findOrFail($attemptId);
    $student = Auth::user()->student;
    
    if ($attempt->student_id !== $student->id) {
        abort(403, 'Unauthorized access to this exam attempt.');
    }
    
    if ($attempt->status === 'submitted') {
        return redirect()->route('student.exam.result', $attempt->id)
            ->with('info', 'This exam has already been submitted.');
    }
    
    // ============ SINGLE SOURCE OF TRUTH FOR SCORE CALCULATION ============
    $answers = $attempt->answers ?? [];
    $questions = $attempt->exam->questions;
    
    $score = 0;
    $correctCount = 0;
    $wrongCount = 0;
    $unansweredCount = 0;
    $answeredCount = 0;
    
    foreach ($questions as $question) {
        if (isset($answers[$question->id]) && !empty(trim($answers[$question->id]))) {
            $answeredCount++;
            
            // Strict comparison — trim both sides and compare case-insensitively if needed
            $studentAnswer = trim($answers[$question->id]);
            $correctAnswer = trim($question->correct_answer);
            
            if ($studentAnswer === $correctAnswer) {
                $score += $question->score;
                $correctCount++;
            } else {
                $wrongCount++;
            }
        } else {
            $unansweredCount++;
        }
    }
    
    $totalScore = $attempt->exam->total_score;
    $percentage = $totalScore > 0 ? ($score / $totalScore) * 100 : 0;
    
    // Update attempt with ALL calculated values
    $attempt->update([
        'completed_at' => now(),
        'score' => $score,
        'total_questions_answered' => $answeredCount,
        'status' => 'submitted',
    ]);
    
    // Delete any existing result first (to prevent duplicates)
    Result::where('student_id', $student->id)
          ->where('exam_id', $attempt->exam->id)
          ->delete();
    
    // Create fresh result
    $result = Result::create([
        'student_id' => $student->id,
        'subject_id' => $attempt->exam->subject_id,
        'exam_id' => $attempt->exam->id,
        'assessment_type' => 'CBT Exam',
        'score' => $score,
        'max_score' => $totalScore,
        'percentage' => round($percentage, 2),
        'grade' => $this->calculateGrade($percentage),
        'remarks' => $this->calculateRemarks($percentage),
        'assessment_date' => now(),
    ]);
    
    \Log::info('Exam submitted', [
        'attempt_id' => $attempt->id,
        'score' => $score,
        'total_score' => $totalScore,
        'percentage' => $percentage,
        'correct' => $correctCount,
        'wrong' => $wrongCount,
        'unanswered' => $unansweredCount,
    ]);
    
    return redirect()->route('student.exam.result', $attempt->id)
        ->with('success', 'Exam submitted successfully!');
}

    /**
     * EXAM RESULT - Show exam result
     */
    // app/Http/Controllers/Student/StudentController.php
public function examResult($attemptId)
{
    $attempt = ExamAttempt::with(['exam', 'exam.subject'])->findOrFail($attemptId);
    $student = Auth::user()->student;
    
    if ($attempt->student_id !== $student->id) {
        abort(403, 'Unauthorized access to this exam result.');
    }
    
    // If not yet submitted, redirect to submit
    if ($attempt->status !== 'submitted') {
        return redirect()->route('student.exam.continue', $attempt->id);
    }
    
    // Find the associated result
    $result = Result::where('exam_id', $attempt->exam_id)
                   ->where('student_id', $student->id)
                   ->first();
    
    // If no result exists (edge case), create it from the stored attempt score
    if (!$result) {
        \Log::warning('Result missing for submitted attempt', ['attempt_id' => $attempt->id]);
        
        $totalScore = $attempt->exam->total_score;
        $percentage = $totalScore > 0 ? ($attempt->score / $totalScore) * 100 : 0;
        
        $result = Result::create([
            'student_id' => $student->id,
            'subject_id' => $attempt->exam->subject_id,
            'exam_id' => $attempt->exam->id,
            'assessment_type' => 'CBT Exam',
            'score' => $attempt->score,
            'max_score' => $totalScore,
            'percentage' => round($percentage, 2),
            'grade' => $this->calculateGrade($percentage),
            'remarks' => $this->calculateRemarks($percentage),
            'assessment_date' => $attempt->completed_at ?? now(),
        ]);
    }
    
    // ============ BUILD DETAILED BREAKDOWN FROM STORED DATA ============
    $answers = $attempt->answers ?? [];
    $questions = $attempt->exam->questions;
    
    $answeredCount = 0;
    $correctCount = 0;
    $wrongCount = 0;
    $unansweredCount = 0;
    $questionBreakdown = [];
    
    foreach ($questions as $question) {
        $studentAnswer = $answers[$question->id] ?? null;
        $correctAnswer = $question->correct_answer;
        
        if ($studentAnswer === null || trim($studentAnswer) === '') {
            $unansweredCount++;
            $status = 'unanswered';
        } else {
            $answeredCount++;
            if (trim($studentAnswer) === trim($correctAnswer)) {
                $correctCount++;
                $status = 'correct';
            } else {
                $wrongCount++;
                $status = 'wrong';
            }
        }
        
        $questionBreakdown[] = [
            'question' => $question,
            'student_answer' => $studentAnswer,
            'correct_answer' => $correctAnswer,
            'status' => $status,
            'score_earned' => $status === 'correct' ? $question->score : 0,
        ];
    }
    
    $totalQuestions = $questions->count();
    
    return view('student.exam-result', compact(
        'attempt',
        'result',
        'questionBreakdown',
        'answeredCount',
        'correctCount',
        'wrongCount',
        'unansweredCount',
        'totalQuestions'
    ));
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
        $reportCard = ReportCard::with(['student', 'class'])->findOrFail($id);
        $student = Auth::user()->student;
        
        if ($reportCard->student_id !== $student->id) {
            abort(403);
        }
        
        return view('student.view-report-card', compact('reportCard'));
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