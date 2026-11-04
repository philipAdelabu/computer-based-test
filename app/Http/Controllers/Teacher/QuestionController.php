<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FuncController;
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
                            ->withCount(['subjects' => function($q) use ($teacherId, $teacherSubjectIds) {
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
            'teacherSubjects',
            'teacherId',
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
    // ============ FIX: Strip theory-only nulls BEFORE validation ============
    if ($request->input('question_type') === 'theory') {
        $request->merge([
            'options' => null,
            'correct_answer' => null,
        ]);
    }

      $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'question_type' => 'required|in:objective,theory',
            'question_text' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3048',
             'options' => 'nullable|array',
             'options.*' => 'nullable|string',
             'correct_answer' => 'nullable|string',
             'option_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3048',
            'score' => 'required|integer|min:1',
            'difficulty' => 'required|in:easy,medium,hard',
        ]);


    // ============ FIX: Additional safety check ============
      if ($validated['question_type'] === 'objective') {
        
        // 1. Check if we are in Image Mode or Text Mode
        $hasImages = $request->hasFile('option_images') && count(array_filter($request->file('option_images'))) > 0;
        
        if (!$hasImages) {
            // --- TEXT MODE CHECKS ---
            $filteredOptions = array_filter($request->input('options', []));
            if (count($filteredOptions) < 2) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'options' => ['You must provide at least 2 non-empty text options for objective questions.'],
                ]);
            }
        } else {
            // --- IMAGE MODE CHECKS ---
            $validImages = array_filter($request->file('option_images'));
         
            if (count($validImages) < 2) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'option_images' => ['You must upload at least 2 images for objective questions.'],
                ]);
            }
        }

        // 2. Check if correct answer is selected
        if (empty($request->input('correct_answer'))) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'correct_answer' => ['You must select a correct answer for objective questions.'],
            ]);
        }
    }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $this->uploadImage($request->file('image'));
        }

      // NEW: Handle Option Images
        $finalCorrectAnswer = null;
        $optionImages = null;
        if ($validated['question_type'] === 'objective') {
            
            $selectedIndex = $validated['correct_answer'];
                // Convert index to letter (0 -> A, 1 -> B, etc.)
            if ($selectedIndex !== null && $selectedIndex !== '') {
                $finalCorrectAnswer = chr(65 + (int) $selectedIndex);
            }

            if ($request->hasFile('option_images')) { 
                $optionImages = [];
                
                foreach ($request->file('option_images') as $index => $file) {
                    if ($file && $file->isValid()) {
                       // $path = $file->store('uploads/options', 'public');
                        $path  = $this->uploadImage($file, 'options');
                         // Store the path using the option index (0, 1, 2, 3)
                        $optionImages[$index] =  $path;
           
                    }
                }
            } 
            
        }

    Question::create([
        'subject_id' => $validated['subject_id'],
        'question_type' => $validated['question_type'],
        'question_text' => $validated['question_text'],
        'image_path' => $imagePath,
        'options' => $validated['question_type'] === 'objective' ? array_values(array_filter($request->input('options', []))) : null,
        'option_images' => $optionImages,
        'correct_answer' => $validated['question_type'] === 'objective' ? $finalCorrectAnswer : null,
        'score' => $validated['score'],
        'difficulty' => $validated['difficulty'],
    ]);

    return redirect()->route('teacher.questions')
        ->with('success', 'Question created successfully.');
}

    


              public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);

        // 1. Handle Theory Questions immediately
        if ($request->input('question_type') === 'theory') {
            $request->merge([
                'options' => null,
                'correct_answer' => null,
                'option_images' => null,
            ]);
        }

        // 2. Validate Request
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'question_type' => 'required|in:objective,theory',
            'question_text' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3048',
            'options' => 'nullable|array',
            'options.*' => 'nullable|string',
            'correct_answer' => 'nullable|string',
            'option_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3048',
            'score' => 'required|integer|min:1',
            'difficulty' => 'required|in:easy,medium,hard',
        ]);

        // 3. Handle Main Question Image
        $imagePath = $question->image_path;
        if ($request->hasFile('image')) {
            if ($imagePath) $this->deleteImage($imagePath);
            $imagePath = $this->uploadImage($request->file('image'), 'questions');
        }

        // 4. Prepare Option Images and Correct Answer
        $optionImages = $question->option_images ?? [];
        if(!is_array($optionImages)) $optionImages = [];
        
        $finalCorrectAnswer = null;
        $finalOptions = null;

        if ($validated['question_type'] === 'objective') {
            
            // Save Correct Answer as Letter (A, B, C)
            $selectedIndex = $request->input('correct_answer'); // 0, 1, 2
            if ($selectedIndex !== null && $selectedIndex !== '') {
                $finalCorrectAnswer = chr(65 + (int) $selectedIndex);
            }

            // Handle Option Images (Uploads & Removals)
            if ($request->has('remove_images')) {
                foreach ($request->input('remove_images') as $index => $remove) {
                    if ($remove == 1 && isset($optionImages[$index])) {
                        $this->deleteImage($optionImages[$index]);
                        unset($optionImages[$index]);
                    }
                }
            }

            if ($request->hasFile('option_images')) {
                foreach ($request->file('option_images') as $index => $file) {
                    if ($file && $file->isValid()) {
                        if (isset($optionImages[$index])) {
                            $this->deleteImage($optionImages[$index]);
                        }
                        $optionImages[$index] = $this->uploadImage($file, 'options');
                    }
                }
            }

            // Clean up array
            $optionImages = array_values($optionImages);
            if (empty($optionImages)) $optionImages = null;

            // Handle Text Options
            $rawOptions = $request->input('options', []);
            $finalOptions = array_values(array_filter($rawOptions));
        }

        // 5. Update the Question
        $question->update([
            'subject_id' => $validated['subject_id'],
            'question_type' => $validated['question_type'],
            'question_text' => $validated['question_text'],
            'image_path' => $imagePath,
            'options' => $finalOptions,
            'option_images' => $optionImages,
            'correct_answer' => $finalCorrectAnswer,
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
            $this->deleteImage('uploads/questions/' . basename($question->image_path));
        }
        
        $question->delete();
        
        return redirect()->route('teacher.questions')
            ->with('success', 'Question deleted successfully.');
    }

    

    /**
     * Upload image to public/uploads/questions folder
     */
     private function uploadImage($file, $folder = 'questions')
     {
            return  FuncController::uploadImage($file, $folder);
     }

    private function deleteImage($imagePath){
         FuncController::deleteImage($imagePath);
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

        try {
            $file = $request->file('file');
            $handle = fopen($file->getRealPath(), 'r');
            
            if (!$handle) {
                throw new \Exception('Could not open the file.');
            }

            // Read the first line
            $headerLine = fgets($handle);
            
            // Remove UTF-8 BOM if Excel added it
            $bom = pack('H*','EFBBBF');
            $headerLine = preg_replace("/^$bom/", '', $headerLine);
            
            // Parse headers
            $header = str_getcsv($headerLine);
            
            // Clean up headers (trim spaces, lowercase)
            $header = array_map(function($col) {
                return strtolower(trim($col));
            }, $header);

            // Check for required columns
            $requiredColumns = ['question', 'correct_answer', 'score'];
            $missingColumns = array_diff($requiredColumns, $header);
            
            if (!empty($missingColumns)) {
                fclose($handle);
                return back()->with('error', 'Missing required column(s): ' . implode(', ', $missingColumns) . '. Please check your CSV headers.');
            }

            $imported = 0;
            $errors = [];
            $rowNumber = 1;

            // Read the rest of the file
            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Map the row data to the header columns
                $data = [];
                foreach ($header as $index => $column) {
                    $data[$column] = trim($row[$index] ?? '');
                }

                // Basic validation
                if (empty($data['question'])) {
                    $errors[] = "Row {$rowNumber}: Missing question text.";
                    continue;
                }
                
                if (empty($data['correct_answer'])) {
                    $errors[] = "Row {$rowNumber}: Missing correct answer.";
                    continue;
                }

                // Determine Question Type (default to objective if not provided)
                $questionType = $data['question_type'] ?? 'objective';
                
                $options = [];
                $optionImages = null;
                $finalCorrectAnswer = $data['correct_answer'];

                // If it's an objective question, gather options
                if (strtolower($questionType) === 'objective') {
                    $options = [
                        $data['option_a'] ?? '',
                        $data['option_b'] ?? '',
                        $data['option_c'] ?? '',
                        $data['option_d'] ?? '',
                    ];
                    $options = array_values(array_filter($options));
                    
                    if (count($options) < 2) {
                        $errors[] = "Row {$rowNumber}: Must have at least 2 options.";
                        continue;
                    }
                    
                    // Ensure the correct answer matches the letter (A, B, C, D)
                    $finalCorrectAnswer = strtoupper($finalCorrectAnswer);
                    if (!in_array($finalCorrectAnswer, ['A', 'B', 'C', 'D', 'E', 'F'])) {
                        $errors[] = "Row {$rowNumber}: Correct answer must be a letter (A, B, C, etc.).";
                        continue;
                    }
                }

                try {
                    Question::create([
                        'subject_id' => $request->subject_id,
                        'question_type' => strtolower($questionType),
                        'question_text' => $data['question'],
                        'options' => strtolower($questionType) === 'objective' ? $options : null,
                        'correct_answer' => strtolower($questionType) === 'objective' ? $finalCorrectAnswer : null,
                        'score' => isset($data['score']) && is_numeric($data['score']) ? (int)$data['score'] : 1,
                        'difficulty' => isset($data['difficulty']) ? strtolower(trim($data['difficulty'])) : 'medium',
                    ]);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row {$rowNumber}: " . $e->getMessage();
                }
            }

            fclose($handle);

            if ($imported === 0 && empty($errors)) {
                return back()->with('error', 'No questions were imported. Please check your file format.');
            }

            $message = "{$imported} question(s) imported successfully.";
            if (!empty($errors)) {
                $message .= " Warnings: " . implode('; ', array_slice($errors, 0, 5));
                return redirect()->route('teacher.questions')->with('warning', $message);
            }

            return redirect()->route('teacher.questions')->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('CSV Import Error: ' . $e->getMessage());
            if (isset($handle) && is_resource($handle)) {
                fclose($handle);
            }
            return back()->with('error', 'Error importing questions: ' . $e->getMessage());
        }
    }

  public function downloadTemplate()
    {
        try {
            // Updated headers
            $headers = ['question_type', 'question', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'score', 'difficulty'];
            
            $sampleData = [
                ['objective', 'What is 2+2?', '3', '4', '5', '6', 'B', '1', 'easy'],
                ['objective', 'What is the capital of Nigeria?', 'Lagos', 'Abuja', 'Kano', 'Ibadan', 'B', '2', 'medium'],
                ['theory', 'Explain the theory of relativity.', '', '', '', '', '', '10', 'hard'],
            ];

            $output = fopen('php://memory', 'w');
            fputs($output, "\xEF\xBB\xBF"); // Add BOM for Excel
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
            return back()->with('error', 'Error generating template.');
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
                                ->get(['id', 'question_text', 'options', 'correct_answer', 'score', 'difficulty', 'question_type']);
            
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