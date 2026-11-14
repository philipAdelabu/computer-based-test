<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\Question;
use App\Models\User;
use App\Models\Student;
use App\Models\ExamAttempt;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::orderBy('name')->paginate(15);
        return view('admin.subjects.index', compact('subjects'));
    }

    public function create()
    {
        // No longer need to pass classes and teachers here
        return view('admin.subjects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // Code must be unique ONLY among master subjects (where class_id is null)
            'code' => 'required|string|max:50', 
            'description' => 'nullable|string',
        ]);

        // Check if code exists globally to avoid conflicts
        if (Subject::where('code', $validated['code'])->exists()) {
            return back()->with('error', 'Subject code already exists.')->withInput();
        }

        // Create the master subject (no class, no teacher)
        Subject::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'] ?? null,
            'class_id' => null,      // Master subject
            'teacher_id' => null,    // Master subject
        ]);

        return redirect()->route('admin.subjects')
            ->with('success', 'Subject added to the pool. You can now assign it to classes.');
    }

    // ============ NEW: ASSIGNMENT METHODS ============
    public function assignForm()
    {
        // Only fetch MASTER subjects (class_id is null) to assign to a class
        $masterSubjects = Subject::whereNull('class_id')->orderBy('name')->get();
        $classes = \App\Models\ClassModel::orderBy('name')->get();
        $teachers = \App\Models\User::where('role', 'teacher')->orderBy('name')->get();
        
        return view('admin.subjects.assign', compact('masterSubjects', 'classes', 'teachers'));
    }

    public function assignStore(Request $request)
    {

      return "hello";
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:users,id',
            'subject_ids' => 'required|array|min:1',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        $class = \App\Models\ClassModel::findOrFail($validated['class_id']);
        $teacher = \App\Models\User::findOrFail($validated['teacher_id']);
        
        $assignedCount = 0;
        $skippedCount = 0;

        foreach ($validated['subject_ids'] as $masterSubjectId) {
            $masterSubject = Subject::findOrFail($masterSubjectId);

            // Check if this subject is ALREADY assigned to this class
            $exists = Subject::where('class_id', $class->id)
                            ->where('name', $masterSubject->name)
                            ->exists();
                            
            if ($exists) {
                $skippedCount++;
                continue; // Skip if already assigned
            }

            // CLONE the master subject and attach the class & teacher
            Subject::create([
                'name' => $masterSubject->name,
                'code' => $masterSubject->code . '-' . strtoupper(Str::random(3)), // Make code unique
                'description' => $masterSubject->description,
                'class_id' => $class->id,
                'teacher_id' => $teacher->id,
            ]);

            $assignedCount++;
        }

        $message = "{$assignedCount} subject(s) assigned to {$class->name}.";
        if ($skippedCount > 0) {
            $message .= " {$skippedCount} subject(s) were skipped because they were already assigned.";
        }

        return redirect()->route('admin.subjects')->with('success', $message);
    }

}
