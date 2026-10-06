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
            'remarks' => 'nullable|string',
        ]);

        // Check for duplicate score entry
        $exists = Result::where('student_id', $validated['student_id'])
                        ->where('subject_id', $validated['subject_id'])
                        ->where('assessment_type', $validated['assessment_type'])
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

    public function bulkUploadStore(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
            'assessment_type' => 'required|string',
            'max_score' => 'required|integer|min:1',
            'assessment_date' => 'required|date',
            'scores' => 'required|array',
            'scores.*' => 'nullable|integer|min:0',
        ]);

        $subject = Subject::findOrFail($validated['subject_id']);
        $students = Student::where('class_id', $validated['class_id'])
                          ->where('status', 'active')
                          ->get();
        
        $saved = 0;
        $skipped = 0;
        
        foreach ($students as $student) {
            $score = $validated['scores'][$student->id] ?? null;
            
            if ($score === null || $score === '') {
                $skipped++;
                continue;
            }
            
            if ($score > $validated['max_score']) {
                continue;
            }
            
            $percentage = ($score / $validated['max_score']) * 100;
            
            Result::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'subject_id' => $validated['subject_id'],
                    'assessment_type' => $validated['assessment_type'],
                    'assessment_date' => $validated['assessment_date'],
                ],
                [
                    'score' => $score,
                    'max_score' => $validated['max_score'],
                    'percentage' => $percentage,
                ]
            );
            
            $saved++;
        }

        return redirect()->route('admin.scores')
            ->with('success', "{$saved} score(s) uploaded successfully. {$skipped} student(s) skipped.");
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