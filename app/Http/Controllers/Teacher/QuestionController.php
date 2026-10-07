<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Question;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{


    public function index(Request $request)
    {
        $teacherId = Auth::id();
        
        // Get filter parameters
        $classId = $request->get('class_id');
        $subjectId = $request->get('subject_id');
        $difficulty = $request->get('difficulty');
        $search = $request->get('search');
        
        // Get teacher's subjects
        $teacherSubjects = Subject::where('teacher_id', $teacherId)
                                ->with('class')
                                ->orderBy('name')
                                ->get();
        
        $teacherSubjectIds = $teacherSubjects->pluck('id');
        
        // Build query - only teacher's subjects
        $query = Question::with(['subject.class'])
                        ->whereIn('subject_id', $teacherSubjectIds);
        
        // Apply filters
        if ($classId) {
            $query->whereHas('subject', function($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }
        
        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }
        
        if ($difficulty) {
            $query->where('difficulty', $difficulty);
        }
        
        if ($search) {
            $query->where('question_text', 'LIKE', "%{$search}%");
        }
        
        $questions = $query->orderBy('subject_id')->latest()->paginate(20)->withQueryString();
        
        // Get classes from teacher's subjects
        $classIds = $teacherSubjects->pluck('class_id')->unique();
        $classes = ClassModel::whereIn('id', $classIds)
                            ->withCount(['subjects' => function($q) use ($teacherId) {
                                $q->where('teacher_id', $teacherId);
                            }])
                            ->orderBy('name')
                            ->get();
        
        // Get all subjects (teacher's only) for filter
        $subjects = $teacherSubjects;
        
        $selectedSubject = $subjectId ? Subject::with('class')->find($subjectId) : null;
        $selectedClass = $classId ? ClassModel::find($classId) : null;
        
        $totalQuestions = Question::whereIn('subject_id', $teacherSubjectIds)->count();
        $filteredCount = $questions->total();
        
        return view('teacher.questions.index', compact(
            'questions',
            'classes',
            'subjects',
            'selectedSubject',
            'selectedClass',
            'totalQuestions',
            'filteredCount',
            'teacherSubjects'
        ));
    }

    public function bySubject($subjectId)
    {
        return redirect()->route('teacher.questions', ['subject_id' => $subjectId]);
    }

    public function byClass($classId)
    {
        return redirect()->route('teacher.questions', ['class_id' => $classId]);
    }

    public function create()
    {
        $teacherId = Auth::id();
        $subjects = Subject::where('teacher_id', $teacherId)->with('class')->get();
        
        if ($subjects->isEmpty()) {
            return redirect()->route('teacher.questions')
                ->with('error', 'You need to be assigned to a subject before adding questions.');
        }
        
        return view('teacher.questions.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'question_text' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string',
            'correct_answer' => 'required|string',
            'score' => 'required|integer|min:1',
            'difficulty' => 'required|in:easy,medium,hard',
        ]);

        $subject = Subject::findOrFail($validated['subject_id']);
        if ($subject->teacher_id !== Auth::id()) {
            return back()->with('error', 'You are not authorized to add questions to this subject.');
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $this->uploadImage($request->file('image'));
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

        return redirect()->route('teacher.questions')
            ->with('success', 'Question created successfully.');
    }

    
        public function update(Request $request, $id)
    {
        $teacherId = Auth::id();
        $question = Question::whereHas('subject', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->findOrFail($id);
        
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'question_text' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string',
            'correct_answer' => 'required|string',
            'score' => 'required|integer|min:1',
            'difficulty' => 'required|in:easy,medium,hard',
        ]);

        $subject = Subject::findOrFail($validated['subject_id']);
        if ($subject->teacher_id !== Auth::id()) {
            return back()->with('error', 'You are not authorized to add questions to this subject.');
        }

        if ($request->hasFile('image')) {
            // Delete old image from public folder
            if ($question->image_path) {
                $oldPath = public_path('uploads/questions/' . basename($question->image_path));
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            
            // Upload new image
            $question->image_path = $this->uploadImage($request->file('image'));
        }

        $question->update([
            'subject_id' => $validated['subject_id'],
            'question_text' => $validated['question_text'],
            'options' => $validated['options'],
            'correct_answer' => $validated['correct_answer'],
            'score' => $validated['score'],
            'difficulty' => $validated['difficulty'],
        ]);

        return redirect()->route('teacher.questions')
            ->with('success', 'Question updated successfully.');
    }

    public function delete($id)
    {
        $teacherId = Auth::id();
        $question = Question::whereHas('subject', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->findOrFail($id);
        
        // Delete image from public folder
        if ($question->image_path) {
            $oldPath = public_path('uploads/questions/' . basename($question->image_path));
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }
        
        $question->delete();
        
        return redirect()->route('teacher.questions')
            ->with('success', 'Question deleted successfully.');
    }

    

    /**
     * Upload image to public/uploads/questions folder
     */
    private function uploadImage($file)
    {
        // Ensure directory exists
        $uploadPath = public_path('uploads/questions');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        
        // Move file to public folder
        $file->move($uploadPath, $filename);
        
        // Return relative path for storage in DB
        return 'uploads/questions/' . $filename;
    }


      


      public function edit($id)
    {
        $teacherId = Auth::id();
        $question = Question::whereHas('subject', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->findOrFail($id);
        
        $subjects = Subject::where('teacher_id', $teacherId)->with('class')->get();
        
        return view('teacher.questions.edit', compact('question', 'subjects'));
    }

    public function import()
    {
        $teacherId = Auth::id();
        $subjects = Subject::where('teacher_id', $teacherId)->with('class')->get();
        
        if ($subjects->isEmpty()) {
            return redirect()->route('teacher.questions')
                ->with('error', 'You need to be assigned to a subject before importing questions.');
        }
        
        return view('teacher.questions.import', compact('subjects'));
    }


public function importCSV(Request $request)
{
    $validator = Validator::make($request->all(), [
        'subject_id' => 'required|exists:subjects,id',
        'file' => 'required|file|mimes:csv,txt|max:5120',
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    // Verify teacher owns the subject
    $subject = Subject::findOrFail($request->subject_id);
    if ($subject->teacher_id !== Auth::id()) {
        return back()->with('error', 'You are not authorized to import questions to this subject.');
    }

    try {
        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        
        if (!$handle) {
            throw new \Exception('Could not open the file.');
        }

        // Read header - handle different CSV formats
        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return back()->with('error', 'The CSV file appears to be empty or invalid.');
        }

        // Clean header
        $header = array_map(function($col) {
            return strtolower(trim($col));
        }, $header);

        // Validate required columns
        $requiredColumns = ['question', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer'];
        $missingColumns = array_diff($requiredColumns, $header);
        
        if (!empty($missingColumns)) {
            fclose($handle);
            return back()->with('error', 'Missing required columns: ' . implode(', ', $missingColumns));
        }

        $imported = 0;
        $errors = [];
        $rowNumber = 1;
        $questions = [];

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Map row data
            $data = [];
            foreach ($header as $index => $column) {
                $data[$column] = trim($row[$index] ?? '');
            }

            // Validate required fields
            if (empty($data['question']) || empty($data['option_a']) || empty($data['option_b'])) {
                $errors[] = "Row {$rowNumber}: Missing required fields (question, option_a, option_b)";
                continue;
            }

            // Build options array
            $options = array_values(array_filter([
                $data['option_a'] ?? '',
                $data['option_b'] ?? '',
                $data['option_c'] ?? '',
                $data['option_d'] ?? '',
            ]));

            if (count($options) < 2) {
                $errors[] = "Row {$rowNumber}: Less than 2 options provided";
                continue;
            }

            // Handle correct answer
            $correctAnswer = $data['correct_answer'] ?? $options[0];
            if (!in_array($correctAnswer, $options)) {
                // Try to find the correct answer by matching value
                $found = false;
                foreach ($options as $option) {
                    if (strtolower(trim($option)) === strtolower(trim($correctAnswer))) {
                        $correctAnswer = $option;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    // If still not found, use first option
                    $correctAnswer = $options[0];
                    $errors[] = "Row {$rowNumber}: Correct answer '{$data['correct_answer']}' not found in options. Using '{$correctAnswer}' as default.";
                }
            }

            // Prepare question data - JSON encode the options array
            $questions[] = [
                'subject_id' => $request->subject_id,
                'question_text' => $data['question'],
                'options' => json_encode($options), // Convert array to JSON
                'correct_answer' => $correctAnswer,
                'score' => isset($data['score']) && is_numeric($data['score']) ? (int)$data['score'] : 1,
                'difficulty' => isset($data['difficulty']) ? strtolower(trim($data['difficulty'])) : 'medium',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $imported++;
        }

        fclose($handle);

        // Bulk insert with JSON encoded options
        if (!empty($questions)) {
            try {
                Question::insert($questions);
            } catch (\Exception $e) {
                Log::error('Bulk insert error: ' . $e->getMessage());
                Log::error('Questions data: ' . json_encode($questions));
                throw $e;
            }
        }

        // Prepare response
        if ($imported === 0) {
            return back()->with('error', 'No questions were imported. Please check your file format.');
        }

        $message = "{$imported} question(s) imported successfully.";
        if (!empty($errors)) {
            $message .= " Warnings: " . implode('; ', array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $message .= " and " . (count($errors) - 5) . " more warnings.";
            }
            return redirect()->route('teacher.questions')->with('warning', $message);
        }

        return redirect()->route('teacher.questions')->with('success', $message);

    } catch (\Exception $e) {
        Log::error('CSV Import Error: ' . $e->getMessage());
        Log::error($e->getTraceAsString());
        
        if (isset($handle) && is_resource($handle)) {
            fclose($handle);
        }
        
        return back()->with('error', 'Error importing questions: ' . $e->getMessage());
    }
}

    public function downloadTemplate()
    {
        try {
            $headers = ['question', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'score', 'difficulty'];
            $sampleData = [
                ['What is 2+2?', '3', '4', '5', '6', '4', '1', 'easy'],
                ['What is the capital of Nigeria?', 'Lagos', 'Abuja', 'Kano', 'Ibadan', 'Abuja', '2', 'medium'],
            ];

            $output = fopen('php://memory', 'w');
            fputs($output, "\xEF\xBB\xBF");
            fputcsv($output, $headers);
            foreach ($sampleData as $row) {
                fputcsv($output, $row);
            }
            
            fseek($output, 0);
            $content = stream_get_contents($output);
            fclose($output);

            return response($content, 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="questions_template.csv"',
            ]);

        } catch (\Exception $e) {
            Log::error('Error generating template: ' . $e->getMessage());
            return back()->with('error', 'Error generating template: ' . $e->getMessage());
        }
    }

    public function getQuestionsBySubject($subjectId)
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
            
            $questions = Question::where('subject_id', $subjectId)
                                ->get(['id', 'question_text', 'options', 'correct_answer', 'score', 'difficulty']);
            
            return response()->json($questions);
            
        } catch (\Exception $e) {
            Log::error('Error fetching questions: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load questions'], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions,id',
        ]);

        $teacherId = Auth::id();
        
        // Verify teacher owns all questions
        $questions = Question::whereIn('id', $request->question_ids)
                            ->whereHas('subject', function($query) use ($teacherId) {
                                $query->where('teacher_id', $teacherId);
                            })
                            ->get();
        
        if ($questions->count() !== count($request->question_ids)) {
            return back()->with('error', 'Some questions are not authorized for deletion.');
        }
        
        // Delete questions
        foreach ($questions as $question) {
            if ($question->image_path) {
                Storage::disk('public')->delete($question->image_path);
            }
            $question->delete();
        }
        
        return redirect()->route('teacher.questions')
            ->with('success', $questions->count() . ' questions deleted successfully.');
    }
}