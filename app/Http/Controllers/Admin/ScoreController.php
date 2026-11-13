<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Result;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScoreController extends Controller
{
    public function index(Request $request)
    {
        $query = Result::with(['student.user', 'subject', 'exam'])
                      ->orderBy('created_at', 'desc');
        
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

        if ($request->has('term') && $request->term) {
            $query->where('term', $request->term);
        }
        
        $results = $query->paginate(20);
        $classes = ClassModel::orderBy('name')->get();
        $subjects = Subject::with('class')->orderBy('name')->get();
        
        return view('admin.scores.index', compact('results', 'classes', 'subjects'));
    }

    public function create()
    {
        $classes = ClassModel::orderBy('name')->get();
        $subjects = Subject::with('class')->orderBy('name')->get();
        $students = Student::with(['user', 'class'])
                          ->where('status', 'active')
                          ->get();
        
        return view('admin.scores.create', compact('classes', 'subjects', 'students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'student_id' => 'required|exists:students,id',
            'assessment_type' => 'required|string',
            'score' => 'required|integer|min:0',
            'max_score' => 'required|integer|min:1',
            'assessment_date' => 'required|date',
            'term' => 'required|string|in:First Term,Second Term,Third Term, 1st Term, 2nd Term, 3rd Term', // NEW
            'remarks' => 'nullable|string',
        ]);

        // Check for duplicate score entry
        $exists = Result::where('student_id', $validated['student_id'])
                        ->where('subject_id', $validated['subject_id'])
                        ->where('assessment_type', $validated['assessment_type'])
                        ->where('term', $validated['term']) // NEW
                        ->whereDate('assessment_date', $validated['assessment_date'])
                        ->exists();
        
        if ($exists) {
            return back()->with('error', 'A score for this assessment already exists for this student.')
                        ->withInput();
        }

        $percentage = ($validated['score'] / $validated['max_score']) * 100;

        Result::create([
            'student_id' => $validated['student_id'],
            'subject_id' => $validated['subject_id'],
            'assessment_type' => $validated['assessment_type'],
            'term' => $validated['term'], // NEW
            'score' => $validated['score'],
            'max_score' => $validated['max_score'],
            'percentage' => $percentage,
            'assessment_date' => $validated['assessment_date'],
            'remarks' => $validated['remarks'] ?? null,
        ]);

        return redirect()->route('admin.scores')
            ->with('success', 'Score uploaded successfully.');
    }

    public function edit($id)
    {
        $result = Result::with(['student.user', 'subject'])->findOrFail($id);
        $classes = ClassModel::orderBy('name')->get();
        $subjects = Subject::with('class')->orderBy('name')->get();
        
        return view('admin.scores.edit', compact('result', 'classes', 'subjects'));
    }

    public function update(Request $request, $id)
    {
        $result = Result::findOrFail($id);
        
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'score' => 'required|integer|min:0',
            'max_score' => 'required|integer|min:1',
            'assessment_type' => 'required|string',
            'assessment_date' => 'required|date',
            'term' => 'required|string|in:First Term,Second Term,Third Term, 1st Term, 2nd Term, 3rd Term', // NEW
            'remarks' => 'nullable|string',
        ]);

        $percentage = ($validated['score'] / $validated['max_score']) * 100;

        $result->update([
            'subject_id' => $validated['subject_id'],
            'score' => $validated['score'],
            'max_score' => $validated['max_score'],
            'percentage' => $percentage,
            'assessment_type' => $validated['assessment_type'],
            'assessment_date' => $validated['assessment_date'],
             'term' => $validated['term'], // NEW
            'remarks' => $validated['remarks'] ?? null,
        ]);

        return redirect()->route('admin.scores')
            ->with('success', 'Score updated successfully.');
    }

    public function delete($id)
    {
        $result = Result::findOrFail($id);
        $result->delete();
        
        return redirect()->route('admin.scores')
            ->with('success', 'Score deleted successfully.');
    }

    public function bulkUpload()
    {
        $classes = ClassModel::orderBy('name')->get();
        $subjects = Subject::with('class')->orderBy('name')->get();
        
        return view('admin.scores.bulk-upload', compact('classes', 'subjects'));
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
            'term' => 'required|string|in:First Term,Second Term,Third Term, 1st Term, 2nd Term, 3rd Term', // NEW
            'scores' => 'required|array',
            'scores.*' => 'nullable|numeric|min:0',
        ]);

        $subject = Subject::findOrFail($validated['subject_id']);
        

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
                              ->where('term', $validated['term']) // NEW
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
                     'term' => $validated['term'], // NEW
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
            return redirect()->route('admin.scores')->with('warning', $message);
        }

        return redirect()->route('admin.scores')->with('success', $message);
    }



    public function getStudentsByClass($classId)
    {
        $students = Student::where('class_id', $classId)
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
}