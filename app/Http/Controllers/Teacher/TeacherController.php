<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Student;
use App\Models\Result;
use App\Models\ExamAttempt;
use App\Models\ReportCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TeacherController extends Controller
{
    public function dashboard()
    {
        $teacherId = Auth::id();
        $subjects = Subject::where('teacher_id', $teacherId)->with('class')->get();
        $subjectIds = $subjects->pluck('id');
        $classIds = $subjects->pluck('class_id')->unique();
        
        // Get current term and academic year
        $currentYear = date('Y');
        
        // ============ BASIC COUNTS ============
        $totalSubjects = $subjects->count();
        $totalQuestions = Question::whereIn('subject_id', $subjectIds)->count();
        $totalTests = Exam::whereIn('subject_id', $subjectIds)
                          ->where('assessment_type', 'test')
                          ->count();
        $totalExams = Exam::whereIn('subject_id', $subjectIds)
                          ->where('assessment_type', 'exam')
                          ->count();
        $totalStudents = Student::whereIn('class_id', $classIds)
                                ->where('status', 'active')
                                ->count();
        
        // ============ RECENT ASSESSMENTS ============
        $recentAssessments = Exam::whereIn('subject_id', $subjectIds)
            ->with(['subject', 'creator'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // ============ PUBLISHED VS DRAFT ============
        $publishedCount = Exam::whereIn('subject_id', $subjectIds)
                              ->where('is_published', true)
                              ->count();
        $draftCount = Exam::whereIn('subject_id', $subjectIds)
                          ->where('is_published', false)
                          ->count();
        
        // ============ ACTIVE NOW ============
        $now = Carbon::now(config('app.timezone'));
        $activeAssessments = Exam::whereIn('subject_id', $subjectIds)
            ->where('is_published', true)
            ->where('status', 'active')
            ->where(function($query) use ($now) {
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
            ->with(['subject'])
            ->orderBy('end_date')
            ->limit(5)
            ->get();
        
        // ============ RECENT ATTEMPTS/SUBMISSIONS ============
        $recentAttempts = ExamAttempt::whereHas('exam', function($query) use ($subjectIds) {
                $query->whereIn('subject_id', $subjectIds);
            })
            ->where('status', 'submitted')
            ->with(['student.user', 'exam'])
            ->orderBy('completed_at', 'desc')
            ->limit(5)
            ->get();
        
        // ============ STATISTICS ============
        $totalAttempts = ExamAttempt::whereHas('exam', function($query) use ($subjectIds) {
                $query->whereIn('subject_id', $subjectIds);
            })
            ->where('status', 'submitted')
            ->count();
        
        $averageScore = ExamAttempt::whereHas('exam', function($query) use ($subjectIds) {
                $query->whereIn('subject_id', $subjectIds);
            })
            ->where('status', 'submitted')
            ->avg('score') ?? 0;
        
        // ============ TOP PERFORMING STUDENTS ============
        $topStudents = Student::whereIn('class_id', $classIds)
            ->where('status', 'active')
            ->with('user')
            ->withCount(['results as average_score' => function($query) {
                $query->select(\DB::raw('avg(percentage)'));
            }])
            ->orderByDesc('average_score')
            ->limit(5)
            ->get();
        
        // ============ SUBJECT PERFORMANCE ============
        $subjectPerformance = [];
        foreach ($subjects as $subject) {
            $testsCount = Exam::where('subject_id', $subject->id)
                              ->where('assessment_type', 'test')
                              ->count();
            $examsCount = Exam::where('subject_id', $subject->id)
                              ->where('assessment_type', 'exam')
                              ->count();
            $questionsCount = Question::where('subject_id', $subject->id)->count();
            $attemptsCount = ExamAttempt::whereHas('exam', function($query) use ($subject) {
                    $query->where('subject_id', $subject->id);
                })
                ->where('status', 'submitted')
                ->count();
            
            $subjectPerformance[] = [
                'subject' => $subject,
                'tests' => $testsCount,
                'exams' => $examsCount,
                'questions' => $questionsCount,
                'attempts' => $attemptsCount,
            ];
        }
        
        return view('teacher.dashboard', compact(
            'totalSubjects',
            'totalQuestions',
            'totalTests',
            'totalExams',
            'totalStudents',
            'recentAssessments',
            'publishedCount',
            'draftCount',
            'activeAssessments',
            'recentAttempts',
            'totalAttempts',
            'averageScore',
            'topStudents',
            'subjectPerformance',
            'subjects'
        ));
    }

    public function subjects()
    {
        $teacherId = Auth::id();
        $subjects = Subject::with(['class', 'teacher'])
                          ->where('teacher_id', $teacherId)
                          ->withCount([
                              'questions',
                              'exams as tests_count' => function($query) {
                                  $query->where('assessment_type', 'test');
                              },
                              'exams as exams_count' => function($query) {
                                  $query->where('assessment_type', 'exam');
                              }
                          ])
                          ->paginate(10);
   
           // ============ GET STUDENTS FOR TEACHER'S CLASSES ============
    // Get unique class IDs from the teacher's subjects
    $classIds = Subject::where('teacher_id', $teacherId)
                       ->pluck('class_id')
                       ->unique()
                       ->toArray();
    
    // Get all students in those classes
    $students = Student::whereIn('class_id', $classIds)
                       ->where('status', 'active')
                       ->with(['user', 'class'])
                       ->orderBy('class_id')
                       ->orderBy('admission_number')
                       ->get();
        
        return view('teacher.subjects', compact('subjects', 'students'));
    }

    public function uploadScores(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'student_id' => 'required|exists:students,id',
            'assessment_type' => 'required|in:test,exam,assignment,project',
            'score' => 'required|integer|min:0',
            'max_score' => 'required|integer|min:1',
            'assessment_date' => 'required|date',
        ]);

        $subject = Subject::findOrFail($request->subject_id);
        
        if ($subject->teacher_id !== Auth::id()) {
            return back()->with('error', 'You are not authorized to upload scores for this subject.');
        }

        $percentage = ($request->score / $request->max_score) * 100;

        Result::create([
            'student_id' => $request->student_id,
            'subject_id' => $request->subject_id,
            'assessment_type' => $request->assessment_type,
            'score' => $request->score,
            'max_score' => $request->max_score,
            'percentage' => $percentage,
            'assessment_date' => $request->assessment_date,
        ]);

        return back()->with('success', 'Score uploaded successfully.');
    }

    public function generateReportCard($studentId)
    {
        $student = Student::with(['class', 'user'])->findOrFail($studentId);
        $results = Result::where('student_id', $studentId)
                        ->whereHas('subject', function($query) {
                            $query->where('teacher_id', Auth::id());
                        })
                        ->get();
        
        return view('teacher.report-card', compact('student', 'results'));
    }
}