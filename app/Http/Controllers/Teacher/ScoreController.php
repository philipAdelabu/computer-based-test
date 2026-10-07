<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Result;
use App\Models\ClassModel;
use App\Models\Exam;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ScoreController extends Controller
{
    public function index(Request $request)
    {
        $teacherId = Auth::id();
        
        // Get teacher's subjects and their class IDs
        $teacherSubjects = Subject::where('teacher_id', $teacherId)->with('class')->get();
        $teacherSubjectIds = $teacherSubjects->pluck('id');
        $classIds = $teacherSubjects->pluck('class_id')->unique();
        
        // Base query — only results for teacher's subjects
        $query = Result::with(['student.user', 'student.class', 'subject', 'exam'])
                       ->whereIn('subject_id', $teacherSubjectIds);
        
        // Filters
        if ($request->has('class_id') && $request->class_id) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }
        
        if ($request->has('subject_id') && $request->subject_id) {
            $query->where('subject_id', $request->subject_id);
        }
        
        if ($request->has('assessment_type') && $request->assessment_type) {
            $query->where('assessment_type', $request->assessment_type);
        }
        
        $results = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        
        $classes = ClassModel::whereIn('id', $classIds)->orderBy('name')->get();
        $subjects = $teacherSubjects;
        
        // Stats
        $totalScores = Result::whereIn('subject_id', $teacherSubjectIds)->count();
        $manualScores = Result::whereIn('subject_id', $teacherSubjectIds)
                              ->whereNull('exam_id')
                              ->count();
        $cbtScores = Result::whereIn('subject_id', $teacherSubjectIds)
                           ->whereNotNull('exam_id')
                           ->count();
        
        return view('teacher.scores.index', compact(
            'results', 'classes', 'subjects',
            'totalScores', 'manualScores', 'cbtScores'
        ));
    }

    public function create()
    {
        $teacherId = Auth::id();
        $subjects = Subject::where('teacher_id', $teacherId)->with('class')->orderBy('name')->get();
        
        if ($subjects->isEmpty()) {
            return redirect()->route('teacher.scores')
                ->with('error', 'You need to be assigned to a subject before uploading scores.');
        }
        
        // Get student count per class
        $classes = ClassModel::whereIn('id', $subjects->pluck('class_id')->unique())
                             ->withCount(['students' => function($q) {
                                 $q->where('status', 'active');
                             }])
                             ->orderBy('name')
                             ->get();
        
        return view('teacher.scores.create', compact('subjects', 'classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'student_id' => 'required|exists:students,id',
            'assessment_type' => 'required|in:test,exam,assignment,project,quiz',
            'score' => 'required|numeric|min:0',
            'max_score' => 'required|numeric|min:1|gte:score',
            'assessment_date' => 'required|date',
            'remarks' => 'nullable|string|max:500',
        ], [
            'max_score.gte' => 'Max score must be greater than or equal to the score.',
        ]);

        // Verify teacher owns the subject
        $subject = Subject::findOrFail($validated['subject_id']);
        if ($subject->teacher_id !== Auth::id()) {
            return back()->with('error', 'You are not authorized to add scores for this subject.')
                         ->withInput();
        }

        // Verify student belongs to the subject's class
        $student = Student::findOrFail($validated['student_id']);
        if ($student->class_id !== $subject->class_id) {
            return back()->with('error', 'Selected student does not belong to this subject\'s class.')
                         ->withInput();
        }

        // Check for duplicate
        $exists = Result::where('student_id', $validated['student_id'])
                        ->where('subject_id', $validated['subject_id'])
                        ->where('assessment_type', $validated['assessment_type'])
                        ->whereDate('assessment_date', $validated['assessment_date'])
                        ->exists();

        if ($exists) {
            return back()->with('error', 'A score for this assessment already exists for this student on this date.')
                         ->withInput();
        }

        $percentage = ($validated['score'] / $validated['max_score']) * 100;

        Result::create([
            'student_id' => $validated['student_id'],
            'subject_id' => $validated['subject_id'],
            'assessment_type' => $validated['assessment_type'],
            'score' => $validated['score'],
            'max_score' => $validated['max_score'],
            'percentage' => round($percentage, 2),
            'assessment_date' => $validated['assessment_date'],
            'remarks' => $validated['remarks'] ?? null,
        ]);

        return redirect()->route('teacher.scores')
            ->with('success', 'Score uploaded successfully.');
    }

    public function edit($id)
    {
        $teacherId = Auth::id();
        $result = Result::with(['student.user', 'subject'])
                        ->whereHas('subject', function($q) use ($teacherId) {
                            $q->where('teacher_id', $teacherId);
                        })
                        ->findOrFail($id);
        
        $subjects = Subject::where('teacher_id', $teacherId)->with('class')->orderBy('name')->get();
        
        return view('teacher.scores.edit', compact('result', 'subjects'));
    }

    public function update(Request $request, $id)
    {
        $teacherId = Auth::id();
        $result = Result::whereHas('subject', function($q) use ($teacherId) {
                            $q->where('teacher_id', $teacherId);
                        })
                        ->findOrFail($id);

        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'assessment_type' => 'required|in:test,exam,assignment,project,quiz',
            'score' => 'required|numeric|min:0',
            'max_score' => 'required|numeric|min:1|gte:score',
            'assessment_date' => 'required|date',
            'remarks' => 'nullable|string|max:500',
        ], [
            'max_score.gte' => 'Max score must be greater than or equal to the score.',
        ]);

        // Verify teacher owns subject
        $subject = Subject::findOrFail($validated['subject_id']);
        if ($subject->teacher_id !== Auth::id()) {
            return back()->with('error', 'You are not authorized to update scores for this subject.');
        }

        $percentage = ($validated['score'] / $validated['max_score']) * 100;

        $result->update([
            'subject_id' => $validated['subject_id'],
            'assessment_type' => $validated['assessment_type'],
            'score' => $validated['score'],
            'max_score' => $validated['max_score'],
            'percentage' => round($percentage, 2),
            'assessment_date' => $validated['assessment_date'],
            'remarks' => $validated['remarks'] ?? null,
        ]);

        return redirect()->route('teacher.scores')
            ->with('success', 'Score updated successfully.');
    }

    public function delete($id)
    {
        $teacherId = Auth::id();
        $result = Result::whereHas('subject', function($q) use ($teacherId) {
                            $q->where('teacher_id', $teacherId);
                        })
                        ->findOrFail($id);
        
        $result->delete();
        
        return redirect()->route('teacher.scores')
            ->with('success', 'Score deleted successfully.');
    }

    /**
     * Bulk upload page
     */
    public function bulkUpload()
    {
        $teacherId = Auth::id();
        $subjects = Subject::where('teacher_id', $teacherId)->with('class')->orderBy('name')->get();
        
        if ($subjects->isEmpty()) {
            return redirect()->route('teacher.scores')
                ->with('error', 'You need to be assigned to a subject before uploading scores.');
        }
        
        return view('teacher.scores.bulk-upload', compact('subjects'));
    }

    /**
     * Store bulk uploaded scores
     */
    public function bulkUploadStore(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'assessment_type' => 'required|in:test,exam,assignment,project,quiz',
            'max_score' => 'required|numeric|min:1',
            'assessment_date' => 'required|date',
            'scores' => 'required|array',
            'scores.*' => 'nullable|numeric|min:0',
        ]);

        $subject = Subject::findOrFail($validated['subject_id']);
        
        // Verify teacher owns subject
        if ($subject->teacher_id !== Auth::id()) {
            return back()->with('error', 'You are not authorized to upload scores for this subject.');
        }

        $saved = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($validated['scores'] as $studentId => $score) {
            // Skip empty scores
            if ($score === null || $score === '') {
                $skipped++;
                continue;
            }

            // Validate score range
            if ($score > $validated['max_score']) {
                $errors[] = "Student #{$studentId}: Score exceeds max score.";
                continue;
            }

            // Verify student belongs to subject's class
            $student = Student::find($studentId);
            if (!$student || $student->class_id !== $subject->class_id) {
                $errors[] = "Student #{$studentId}: Not in this subject's class.";
                continue;
            }

            $percentage = ($score / $validated['max_score']) * 100;

            // Check if score already exists
            $existing = Result::where('student_id', $studentId)
                              ->where('subject_id', $validated['subject_id'])
                              ->where('assessment_type', $validated['assessment_type'])
                              ->whereDate('assessment_date', $validated['assessment_date'])
                              ->first();

            if ($existing) {
                $existing->update([
                    'score' => $score,
                    'max_score' => $validated['max_score'],
                    'percentage' => round($percentage, 2),
                ]);
                $updated++;
            } else {
                Result::create([
                    'student_id' => $studentId,
                    'subject_id' => $validated['subject_id'],
                    'assessment_type' => $validated['assessment_type'],
                    'score' => $score,
                    'max_score' => $validated['max_score'],
                    'percentage' => round($percentage, 2),
                    'assessment_date' => $validated['assessment_date'],
                ]);
                $saved++;
            }
        }

        $message = "{$saved} new score(s) added, {$updated} updated, {$skipped} skipped.";
        if (!empty($errors)) {
            $message .= " Errors: " . implode('; ', array_slice($errors, 0, 3));
            if (count($errors) > 3) {
                $message .= " and " . (count($errors) - 3) . " more.";
            }
            return redirect()->route('teacher.scores')->with('warning', $message);
        }

        return redirect()->route('teacher.scores')->with('success', $message);
    }

    /**
     * AJAX: Get students by subject
     */
    public function getStudentsBySubject($subjectId)
    {
        $teacherId = Auth::id();
        $subject = Subject::where('id', $subjectId)
                          ->where('teacher_id', $teacherId)
                          ->first();

        if (!$subject) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $students = Student::where('class_id', $subject->class_id)
                           ->where('status', 'active')
                           ->with('user')
                           ->get()
                           ->map(function($student) {
                               return [
                                   'id' => $student->id,
                                   'name' => $student->user->name,
                                   'admission_number' => $student->admission_number,
                               ];
                           });

        return response()->json($students);
    }

    /**
     * AJAX: Get subjects by class
     */
    public function getSubjectsByClass($classId)
    {
        $teacherId = Auth::id();
        $subjects = Subject::where('class_id', $classId)
                           ->where('teacher_id', $teacherId)
                           ->get(['id', 'name', 'code']);

        return response()->json($subjects);
    }
}
