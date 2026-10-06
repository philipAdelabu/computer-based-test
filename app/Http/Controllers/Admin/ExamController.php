<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Student;
use App\Models\ExamAttempt;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $query = Exam::with(['subject', 'creator'])
                     ->orderBy('created_at', 'desc');
        
        // Filter by assessment type
        if ($request->has('type') && in_array($request->type, ['test', 'exam'])) {
            $query->where('assessment_type', $request->type);
        }
        
        // Filter by subject
        if ($request->has('subject_id') && $request->subject_id) {
            $query->where('subject_id', $request->subject_id);
        }
        
        // Filter by class
        if ($request->has('class_id') && $request->class_id) {
            $query->whereHas('subject', function($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('is_published', $request->status === 'published');
        }
        
        $exams = $query->paginate(15);
        $subjects = Subject::with('class')->orderBy('name')->get();
        $classes = ClassModel::orderBy('name')->get();
        
        return view('admin.exams.index', compact('exams', 'subjects', 'classes'));
    }

    public function create(Request $request)
    {
        $subjects = Subject::with(['class', 'teacher'])->orderBy('name')->get();
        
        if ($subjects->isEmpty()) {
            return redirect()->route('admin.exams')
                ->with('error', 'You need to create subjects before creating assessments.');
        }
        
        $defaultType = $request->get('type', 'exam');
        
        return view('admin.exams.create', compact('subjects', 'defaultType'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'assessment_type' => 'required|in:test,exam',
            'max_marks' => 'required|integer|min:1|max:200',
            'benchmark' => 'required|integer|min:0|max:100',
            'term' => 'required|string|max:50',
            'academic_year' => 'required|integer|min:2020|max:2100',
            'description' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,id',
            'duration_minutes' => 'required|integer|min:5|max:180',
            'schedule_type' => 'required|in:no_date,single_date,date_range',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'available_from' => 'nullable|date',
            'available_to' => 'nullable|date|after:available_from',
            'instructions' => 'nullable|string',
            'question_ids' => 'required|array|min:1',
            'question_ids.*' => 'exists:questions,id',
            'is_published' => 'boolean',
            'max_attempts' => 'integer|min:1|max:10',
            'show_answers_after_completion' => 'boolean',
        ]);

        // Verify questions belong to subject and deduplicate
        $questionIds = array_unique($validated['question_ids']);
        $questions = Question::whereIn('id', $questionIds)
                             ->where('subject_id', $validated['subject_id'])
                             ->get();
        
        if ($questions->count() !== count($questionIds)) {
            return back()->with('error', 'Some selected questions do not belong to this subject.')
                         ->withInput();
        }

        $totalQuestions = $questions->count();
        $totalScore = $questions->sum('score');

        $examData = [
            'title' => $validated['title'],
            'assessment_type' => $validated['assessment_type'],
            'max_marks' => $validated['max_marks'],
            'benchmark' => $validated['benchmark'],
            'term' => $validated['term'],
            'academic_year' => $validated['academic_year'],
            'description' => $validated['description'] ?? null,
            'subject_id' => $validated['subject_id'],
            'duration_minutes' => $validated['duration_minutes'],
            'total_questions' => $totalQuestions,
            'total_score' => $totalScore,
            'schedule_type' => $validated['schedule_type'],
            'status' => 'active',
            'created_by_role' => 'admin',
            'created_by' => Auth::id(),
            'instructions' => $validated['instructions'] ?? null,
            'is_published' => $validated['is_published'] ?? false,
            'max_attempts' => $validated['max_attempts'] ?? 1,
            'passing_score' => $validated['benchmark'],
            'show_answers_after_completion' => $validated['show_answers_after_completion'] ?? false,
        ];

        // Set dates based on schedule type
        switch ($validated['schedule_type']) {
            case 'single_date':
                $examData['start_date'] = Carbon::parse($validated['start_date'])->timezone(config('app.timezone'));
                $examData['end_date'] = !empty($validated['end_date']) 
                    ? Carbon::parse($validated['end_date'])->timezone(config('app.timezone'))
                    : Carbon::parse($validated['start_date'])->addDay()->timezone(config('app.timezone'));
                break;
            case 'date_range':
                $examData['available_from'] = Carbon::parse($validated['available_from'])->timezone(config('app.timezone'));
                $examData['available_to'] = Carbon::parse($validated['available_to'])->timezone(config('app.timezone'));
                break;
        }

        $exam = Exam::create($examData);

        // Attach questions
        $attachData = [];
        foreach ($questionIds as $index => $questionId) {
            $attachData[$questionId] = ['question_order' => $index + 1];
        }
        $exam->questions()->attach($attachData);

        return redirect()->route('admin.exams')
            ->with('success', $exam->assessment_type === 'test' 
                ? 'Test created successfully.' 
                : 'Exam created successfully.');
    }

    public function show($id)
    {
        $exam = Exam::with(['subject', 'questions', 'creator'])->findOrFail($id);
        
        $attempts = ExamAttempt::where('exam_id', $id)
                              ->with('student.user')
                              ->paginate(20);
        
        return view('admin.exams.show', compact('exam', 'attempts'));
    }

    public function edit($id)
    {
        $exam = Exam::with(['subject', 'questions'])->findOrFail($id);
        
        if ($exam->status === 'completed' || $exam->status === 'active') {
            return redirect()->route('admin.exams')
                ->with('error', 'Cannot edit an assessment that is active or completed.');
        }
        
        $subjects = Subject::with(['class', 'teacher'])->orderBy('name')->get();
        
        return view('admin.exams.edit', compact('exam', 'subjects'));
    }

    public function update(Request $request, $id)
    {
        $exam = Exam::with(['subject', 'questions'])->findOrFail($id);
        
        if ($exam->status === 'completed' || $exam->status === 'active') {
            return back()->with('error', 'Cannot update an assessment that is active or completed.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'assessment_type' => 'required|in:test,exam',
            'max_marks' => 'required|integer|min:1|max:200',
            'benchmark' => 'required|integer|min:0|max:100',
            'term' => 'required|string|max:50',
            'academic_year' => 'required|integer|min:2020|max:2100',
            'description' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,id',
            'duration_minutes' => 'required|integer|min:5|max:180',
            'schedule_type' => 'required|in:no_date,single_date,date_range',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'available_from' => 'nullable|date',
            'available_to' => 'nullable|date|after:available_from',
            'instructions' => 'nullable|string',
            'question_ids' => 'required|array|min:1',
            'question_ids.*' => 'exists:questions,id',
            'is_published' => 'boolean',
            'max_attempts' => 'integer|min:1|max:10',
            'show_answers_after_completion' => 'boolean',
        ]);

        $questionIds = array_unique($validated['question_ids']);
        $questions = Question::whereIn('id', $questionIds)
                             ->where('subject_id', $validated['subject_id'])
                             ->get();
        
        $totalQuestions = $questions->count();
        $totalScore = $questions->sum('score');

        $examData = [
            'title' => $validated['title'],
            'assessment_type' => $validated['assessment_type'],
            'max_marks' => $validated['max_marks'],
            'benchmark' => $validated['benchmark'],
            'term' => $validated['term'],
            'academic_year' => $validated['academic_year'],
            'description' => $validated['description'] ?? null,
            'subject_id' => $validated['subject_id'],
            'duration_minutes' => $validated['duration_minutes'],
            'total_questions' => $totalQuestions,
            'total_score' => $totalScore,
            'schedule_type' => $validated['schedule_type'],
            'instructions' => $validated['instructions'] ?? null,
            'is_published' => $validated['is_published'] ?? false,
            'max_attempts' => $validated['max_attempts'] ?? 1,
            'passing_score' => $validated['benchmark'],
            'show_answers_after_completion' => $validated['show_answers_after_completion'] ?? false,
        ];

        switch ($validated['schedule_type']) {
            case 'single_date':
                $examData['start_date'] = Carbon::parse($validated['start_date'])->timezone(config('app.timezone'));
                $examData['end_date'] = !empty($validated['end_date']) 
                    ? Carbon::parse($validated['end_date'])->timezone(config('app.timezone'))
                    : Carbon::parse($validated['start_date'])->addDay()->timezone(config('app.timezone'));
                $examData['available_from'] = null;
                $examData['available_to'] = null;
                break;
            case 'date_range':
                $examData['available_from'] = Carbon::parse($validated['available_from'])->timezone(config('app.timezone'));
                $examData['available_to'] = Carbon::parse($validated['available_to'])->timezone(config('app.timezone'));
                $examData['start_date'] = null;
                $examData['end_date'] = null;
                break;
            case 'no_date':
                $examData['start_date'] = null;
                $examData['end_date'] = null;
                $examData['available_from'] = null;
                $examData['available_to'] = null;
                break;
        }

        $exam->update($examData);

        // Sync questions
        $exam->questions()->detach();
        $attachData = [];
        foreach ($questionIds as $index => $questionId) {
            $attachData[$questionId] = ['question_order' => $index + 1];
        }
        $exam->questions()->attach($attachData);

        return redirect()->route('admin.exams.show', $exam->id)
            ->with('success', 'Assessment updated successfully.');
    }

    public function delete($id)
    {
        $exam = Exam::findOrFail($id);
        
        if ($exam->status === 'active' || $exam->status === 'completed') {
            return back()->with('error', 'Cannot delete an assessment that is active or completed.');
        }
        
        $exam->questions()->detach();
        $exam->delete();
        
        return redirect()->route('admin.exams')
            ->with('success', 'Assessment deleted successfully.');
    }

    public function publish($id)
    {
        $exam = Exam::findOrFail($id);
        
        if ($exam->status === 'completed') {
            return back()->with('error', 'Cannot publish a completed assessment.');
        }
        
        $exam->update(['is_published' => true, 'status' => 'active']);
        
        return back()->with('success', 'Assessment published successfully.');
    }

    public function unpublish($id)
    {
        $exam = Exam::findOrFail($id);
        
        if ($exam->status === 'completed') {
            return back()->with('error', 'Cannot unpublish a completed assessment.');
        }
        
        $exam->update(['is_published' => false]);
        
        return back()->with('success', 'Assessment unpublished successfully.');
    }

    public function getQuestions($subjectId)
    {
        $questions = Question::where('subject_id', $subjectId)
                            ->get(['id', 'question_text', 'score', 'difficulty']);
        return response()->json($questions);
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