<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Student;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ExamController extends Controller
{
    public function index()
    {
        $teacherId = Auth::id();
        $exams = Exam::whereHas('subject', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->with(['subject', 'creator'])
        ->orderBy('created_at', 'desc')
        ->paginate(15);
        
        return view('teacher.exams.index', compact('exams'));
    }

  // app/Http/Controllers/Teacher/ExamController.php
// Update the create and store methods

public function create()
{
    $teacherId = Auth::id();
    $subjects = Subject::where('teacher_id', $teacherId)->with('class')->get();
    
    if ($subjects->isEmpty()) {
        return redirect()->route('teacher.exams')
            ->with('error', 'You need to be assigned to a subject before creating exams.');
    }
    
    return view('teacher.exams.create', compact('subjects'));
}

public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'subject_id' => 'required|exists:subjects,id',
        'duration_minutes' => 'required|integer|min:5|max:180',
        'schedule_type' => 'required|in:no_date,single_date,date_range',
        'start_date' => 'nullable|date|after:now',
        'end_date' => 'nullable|date|after:start_date',
        'available_from' => 'nullable|date|after:now',
        'available_to' => 'nullable|date|after:available_from',
        'instructions' => 'nullable|string',
        'question_ids' => 'required|array|min:1',
        'question_ids.*' => 'exists:questions,id',
        'is_published' => 'boolean',
        'max_attempts' => 'integer|min:1|max:10',
        'passing_score' => 'nullable|integer|min:0|max:100',
        'show_answers_after_completion' => 'boolean',
    ]);

    // Verify teacher owns the subject
    $subject = Subject::findOrFail($validated['subject_id']);
    if ($subject->teacher_id !== Auth::id()) {
        return back()->with('error', 'You are not authorized to create exams for this subject.');
    }

    // Validate based on schedule type
    if ($validated['schedule_type'] === 'single_date') {
        if (empty($validated['start_date'])) {
            return back()->with('error', 'Start date is required for single date schedule.');
        }
        if (empty($validated['end_date'])) {
            $validated['end_date'] = Carbon::parse($validated['start_date'])->addDay();
        }
    } elseif ($validated['schedule_type'] === 'date_range') {
        if (empty($validated['available_from']) || empty($validated['available_to'])) {
            return back()->with('error', 'Both from and to dates are required for date range schedule.');
        }
    }

    // Calculate total questions and score
    $questions = Question::whereIn('id', $validated['question_ids'])->get();
    $totalQuestions = $questions->count();
    $totalScore = $questions->sum('score');

    // Prepare exam data
    $examData = [
        'title' => $validated['title'],
        'description' => $validated['description'],
        'subject_id' => $validated['subject_id'],
        'duration_minutes' => $validated['duration_minutes'],
        'total_questions' => $totalQuestions,
        'total_score' => $totalScore,
        'schedule_type' => $validated['schedule_type'],
        'status' => 'active',
        'created_by_role' => 'teacher',
        'created_by' => Auth::id(),
        'instructions' => $validated['instructions'],
        'is_published' => $validated['is_published'] ?? false,
        'max_attempts' => $validated['max_attempts'] ?? 1,
        'passing_score' => $validated['passing_score'] ?? null,
        'show_answers_after_completion' => $validated['show_answers_after_completion'] ?? false,
    ];

    // Set dates based on schedule type
    switch ($validated['schedule_type']) {
        case 'single_date':
            $examData['start_date'] = Carbon::parse($validated['start_date'])->timezone(config('app.timezone'));
            $examData['end_date'] = Carbon::parse($validated['end_date'])->timezone(config('app.timezone'));
            $examData['available_from'] = null;
            $examData['available_to'] = null;
            break;
            
        case 'date_range':
            $examData['start_date'] = null;
            $examData['end_date'] = null;
            $examData['available_from'] = Carbon::parse($validated['available_from'])->timezone(config('app.timezone'));
            $examData['available_to'] = Carbon::parse($validated['available_to'])->timezone(config('app.timezone'));
            break;
            
        case 'no_date':
        default:
            $examData['start_date'] = null;
            $examData['end_date'] = null;
            $examData['available_from'] = null;
            $examData['available_to'] = null;
            break;
    }

    // Create exam
    $exam = Exam::create($examData);

    // Attach questions with order
    foreach ($validated['question_ids'] as $index => $questionId) {
        $exam->questions()->attach($questionId, ['question_order' => $index + 1]);
    }

    return redirect()->route('teacher.exams')
        ->with('success', 'Exam created successfully.');
}

    public function show($id)
    {
        $teacherId = Auth::id();
        $exam = Exam::with(['subject', 'questions', 'creator'])
                   ->whereHas('subject', function($query) use ($teacherId) {
                       $query->where('teacher_id', $teacherId);
                   })
                   ->findOrFail($id);
        
        $attempts = ExamAttempt::where('exam_id', $id)
                              ->with('student.user')
                              ->paginate(20);
        
        return view('teacher.exams.show', compact('exam', 'attempts'));
    }

    public function edit($id)
    {
        $teacherId = Auth::id();
        $exam = Exam::with(['subject', 'questions'])
                   ->whereHas('subject', function($query) use ($teacherId) {
                       $query->where('teacher_id', $teacherId);
                   })
                   ->findOrFail($id);
        
        if ($exam->status === 'completed' || $exam->status === 'active') {
            return redirect()->route('teacher.exams')
                ->with('error', 'Cannot edit an exam that is active or completed.');
        }
        
        $subjects = Subject::where('teacher_id', $teacherId)->with('class')->get();
        $availableQuestions = Question::where('subject_id', $exam->subject_id)->get();
        
        return view('teacher.exams.edit', compact('exam', 'subjects', 'availableQuestions'));
    }

    public function update(Request $request, $id)
    {
        $teacherId = Auth::id();
        $exam = Exam::with(['subject', 'questions'])
                   ->whereHas('subject', function($query) use ($teacherId) {
                       $query->where('teacher_id', $teacherId);
                   })
                   ->findOrFail($id);
        
        if ($exam->status === 'completed' || $exam->status === 'active') {
            return back()->with('error', 'Cannot update an exam that is active or completed.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,id',
            'duration_minutes' => 'required|integer|min:5|max:180',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'instructions' => 'nullable|string',
            'question_ids' => 'required|array|min:1',
            'question_ids.*' => 'exists:questions,id',
            'is_published' => 'boolean',
        ]);

        // Calculate total questions and score
        $questions = Question::whereIn('id', $validated['question_ids'])->get();
        $totalQuestions = $questions->count();
        $totalScore = $questions->sum('score');

        // Update exam
        $exam->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'subject_id' => $validated['subject_id'],
            'duration_minutes' => $validated['duration_minutes'],
            'total_questions' => $totalQuestions,
            'total_score' => $totalScore,
            'start_date' => Carbon::parse($validated['start_date']),
            'end_date' => Carbon::parse($validated['end_date']),
            'instructions' => $validated['instructions'],
            'is_published' => $validated['is_published'] ?? false,
        ]);

        // Sync questions with order
        $exam->questions()->detach();
        foreach ($validated['question_ids'] as $index => $questionId) {
            $exam->questions()->attach($questionId, ['question_order' => $index + 1]);
        }

        return redirect()->route('teacher.exams.show', $exam->id)
            ->with('success', 'Exam updated successfully.');
    }

    public function delete($id)
    {
        $teacherId = Auth::id();
        $exam = Exam::whereHas('subject', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->findOrFail($id);
        
        if ($exam->status === 'active' || $exam->status === 'completed') {
            return back()->with('error', 'Cannot delete an exam that is active or completed.');
        }
        
        $exam->questions()->detach();
        $exam->delete();
        
        return redirect()->route('teacher.exams')
            ->with('success', 'Exam deleted successfully.');
    }

    public function publish($id)
    {
        $teacherId = Auth::id();
        $exam = Exam::whereHas('subject', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->findOrFail($id);
        
        if ($exam->status === 'completed') {
            return back()->with('error', 'Cannot publish a completed exam.');
        }
        
        $exam->update(['is_published' => true, 'status' => 'active']);
        
        return back()->with('success', 'Exam published successfully. Students can now take the exam.');
    }

    public function unpublish($id)
    {
        $teacherId = Auth::id();
        $exam = Exam::whereHas('subject', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->findOrFail($id);
        
        if ($exam->status === 'completed') {
            return back()->with('error', 'Cannot unpublish a completed exam.');
        }
        
        $exam->update(['is_published' => false]);
        
        return back()->with('success', 'Exam unpublished successfully.');
    }

 public function getQuestions($subjectId)
{
    try {
        $teacherId = Auth::id();
        
        // Verify teacher owns the subject
        $subject = Subject::where('id', $subjectId)
                         ->where('teacher_id', $teacherId)
                         ->first();
        
        if (!$subject) {
            return response()->json(['error' => 'Subject not found or unauthorized'], 404);
        }
        
        // Get only questions that belong to this subject
        $questions = Question::where('subject_id', $subjectId)
                            ->get(['id', 'question_text', 'score', 'difficulty']);
        
        return response()->json($questions);
        
    } catch (\Exception $e) {
        Log::error('Error fetching questions: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to load questions'], 500);
    }
}

    public function getStudents($examId)
    {
        $exam = Exam::with('subject.class')->findOrFail($examId);
        $students = Student::where('class_id', $exam->subject->class_id)
                          ->with('user')
                          ->get(['id', 'user_id', 'admission_number']);
        return response()->json($students);
    }
}