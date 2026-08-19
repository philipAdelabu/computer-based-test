<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Question;
use App\Imports\QuestionsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::with('subject')->paginate(20);
        return view('admin.questions.index', compact('questions'));
    }

    public function create()
    {
        $subjects = Subject::with('class')->get();
        return view('admin.questions.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'question_text' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string',
            'correct_answer' => 'required|string',
            'score' => 'required|integer|min:1',
            'difficulty' => 'required|in:easy,medium,hard',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('questions', 'public');
        }

        Question::create([
            'subject_id' => $validated['subject_id'],
            'question_text' => $validated['question_text'],
            'image_path' => $imagePath,
            'options' => $validated['options'],
            'correct_answer' => $validated['correct_answer'],
            'score' => $validated['score'],
            'difficulty' => $validated['difficulty'],
        ]);

        return redirect()->route('admin.questions')
            ->with('success', 'Question created successfully.');
    }

    public function edit($id)
    {
        $question = Question::findOrFail($id);
        $subjects = Subject::with('class')->get();
        return view('admin.questions.edit', compact('question', 'subjects'));
    }

    public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);
        
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'question_text' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string',
            'correct_answer' => 'required|string',
            'score' => 'required|integer|min:1',
            'difficulty' => 'required|in:easy,medium,hard',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($question->image_path) {
                Storage::disk('public')->delete($question->image_path);
            }
            $imagePath = $request->file('image')->store('questions', 'public');
            $question->image_path = $imagePath;
        }

        $question->update([
            'subject_id' => $validated['subject_id'],
            'question_text' => $validated['question_text'],
            'options' => $validated['options'],
            'correct_answer' => $validated['correct_answer'],
            'score' => $validated['score'],
            'difficulty' => $validated['difficulty'],
        ]);

        return redirect()->route('admin.questions')
            ->with('success', 'Question updated successfully.');
    }

    public function delete($id)
    {
        $question = Question::findOrFail($id);
        if ($question->image_path) {
            Storage::disk('public')->delete($question->image_path);
        }
        $question->delete();
        
        return redirect()->route('admin.questions')
            ->with('success', 'Question deleted successfully.');
    }

    public function import()
    {
        $subjects = Subject::with('class')->get();
        return view('admin.questions.import', compact('subjects'));
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            $import = new QuestionsImport($request->subject_id);
            Excel::import($import, $request->file('file'));
            
            return redirect()->route('admin.questions')
                ->with('success', 'Questions imported successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing questions: ' . $e->getMessage());
        }
    }
}