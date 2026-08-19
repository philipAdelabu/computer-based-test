<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Student;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{
    public function dashboard()
    {
        $teacherId = Auth::id();
        $subjects = Subject::where('teacher_id', $teacherId)->get();
        $subjectIds = $subjects->pluck('id');
        
        $totalSubjects = $subjects->count();
        $totalQuestions = Question::whereIn('subject_id', $subjectIds)->count();
        $totalExams = Exam::whereIn('subject_id', $subjectIds)->count();
        $totalStudents = Student::whereIn('class_id', $subjects->pluck('class_id'))->count();
        
        return view('teacher.dashboard', compact(
            'totalSubjects', 'totalQuestions', 'totalExams', 'totalStudents'
        ));
    }

    public function subjects()
    {
        $subjects = Subject::with(['class', 'teacher'])
                          ->where('teacher_id', Auth::id())
                          ->paginate(10);
        return view('teacher.subjects', compact('subjects'));
    }

    public function uploadScores(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'student_id' => 'required|exists:students,id',
            'score' => 'required|integer|min:0',
            'max_score' => 'required|integer|min:1',
            'assessment_type' => 'required|string',
            'assessment_date' => 'required|date',
        ]);

        $subject = Subject::findOrFail($request->subject_id);
        
        // Verify teacher owns this subject
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