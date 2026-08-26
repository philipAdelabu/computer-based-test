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
        // Apply auth middleware to all methods
       // $this->middleware('auth');
        
        // Share available exams count with all student views
        $this->shareAvailableExamsCount();
    }

    /**
     * Share available exams count with all views
     */
    protected function shareAvailableExamsCount()
    {
        // Use view composer to share data
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
        
        // Get the student's class and subjects
        $class = $student->class;
        $subjects = $class ? $class->subjects : collect();
        
        // Get available exams for the student's class
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
        
        // Get upcoming exams
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
        
        // Count statistics
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

// app/Http/Controllers/Student/StudentController.php
// Update the exams method

public function exams()
{
    $student = Auth::user()->student;
    
    // Available exams (active and within date range)
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
    
    // Upcoming exams
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
    
    // Completed exams - ONLY get submitted attempts
    $completedExams = ExamAttempt::where('student_id', $student->id)
                                ->where('status', 'submitted')
                                ->with(['exam', 'exam.subject'])
                                ->latest()
                                ->paginate(10);
    
    return view('student.exams', compact('availableExams', 'upcomingExams', 'completedExams'));
}

     // app/Http/Controllers/Student/StudentController.php
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
    
    // Create new attempt with attempt number
    $attempt = ExamAttempt::create([
        'exam_id' => $examId,
        'student_id' => $student->id,
        'attempt_number' => $attemptCount + 1,
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
        if (!($attempt->exam->status == 'active')) {
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