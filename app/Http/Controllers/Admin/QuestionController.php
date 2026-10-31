<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FuncController;
use App\Models\Subject;
use App\Models\Question;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Imports\QuestionsImport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
        public function index(Request $request)
    {
        // Get filter parameters
        $classId = $request->get('class_id');
        $subjectId = $request->get('subject_id');
        $difficulty = $request->get('difficulty');
        $search = $request->get('search');
        
        // Build query
        $query = Question::with(['subject.class']);
        
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
        
        // Get statistics per class and subject
        $classes = ClassModel::with(['subjects' => function($q) {
            $q->withCount('questions');
        }])->withCount('subjects')->orderBy('name')->get();
        
        // Get all subjects for filter dropdown
        $subjects = Subject::with('class')->orderBy('name')->get();
        
        // Selected subject (if filtering by subject)
        $selectedSubject = $subjectId ? Subject::with('class')->find($subjectId) : null;
        $selectedClass = $classId ? ClassModel::find($classId) : null;
        
        // Group counts
        $totalQuestions = Question::count();
        $filteredCount = $questions->total();
        
        return view('admin.questions.index', compact(
            'questions',
            'classes',
            'subjects',
            'selectedSubject',
            'selectedClass',
            'totalQuestions',
            'filteredCount'
        ));
    }

    /**
     * View questions by subject
     */
    public function bySubject($subjectId)
    {
        return redirect()->route('admin.questions', ['subject_id' => $subjectId]);
    }

    /**
     * View questions by class
     */
    public function byClass($classId)
    {
        return redirect()->route('admin.questions', ['class_id' => $classId]);
    }

    public function create()
    {
        $subjects = Subject::with('class')->get();
        return view('admin.questions.create', compact('subjects'));
    }

    // =====================================================
    // STORE
    // =====================================================
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

    
     // =========================================================================
    // POST-VALIDATION CUSTOM CHECKS
    // =========================================================================
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
    $optionImages = null;
    if ($request->hasFile('image')) {
        $imagePath = $this->uploadImage($request->file('image'), 'questions');
    }

        // Handle Option Images and Correct Answer
        $finalCorrectAnswer = null;
        if ($validated['question_type'] === 'objective') {
            // The dropdown will submit either the text (e.g., "Paris") or the letter (e.g., "A")
            $finalCorrectAnswer = $validated['correct_answer'];

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

    return redirect()->route('admin.questions')
        ->with('success', 'Question created successfully.');
}

    // =====================================================
    // EDIT
    // =====================================================
    public function edit($id)
    {
        $question = Question::findOrFail($id);
        $subjects = Subject::with('class')->get();
        return view('admin.questions.edit', compact('question', 'subjects'));
    }

    // =====================================================
    // UPDATE
    // =====================================================
    public function update(Request $request, $id)
    {


        $question = Question::findOrFail($id);
        
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

  

      // ============ FIX: Additional safety check for Objective ============

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
           $request->option_images = null;
        } else {
            // --- IMAGE MODE CHECKS ---
            $validImages = array_filter($request->file('option_images'));
         
            if (count($validImages) < 2) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'option_images' => ['You must upload at least 2 images for objective questions.'],
                ]);
            }

            $validated['options'] = null; // Clear text options if images are used
        }

        // 2. Check if correct answer is selected
        if (empty($request->input('correct_answer'))) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'correct_answer' => ['You must select a correct answer for objective questions.'],
            ]);
        }
    }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image from public folder
            if ($question->image_path) {
                $this->deleteImage($question->image_path);
            }
            
            // Upload new image
            $question->image_path = $this->uploadImage($request->file('image'), 'questions');
        }

        // NEW: Handle Option Images
          // Inside update() method, after validation...

            $optionImages = $question->option_images ?? []; // Start with existing images
            $finalCorrectAnswer = $validated['correct_answer'];

            if ($validated['question_type'] === 'objective') {
                
                // 1. Handle Removals
                if ($request->has('remove_images')) {
                    foreach ($request->input('remove_images') as $index => $remove) {
                        if ($remove == 1) {
                            // Delete the file from storage
                            if (isset($optionImages[$index])) {
                                 $oldPath = $optionImages[$index];
                                $this->deleteImage( $oldPath);
                            }
                        
                        }
                    }
                }

                // 2. Handle New Uploads (Replacements)
                if ($request->hasFile('option_images')) {
                    foreach ($request->file('option_images') as $index => $file) {
                        if ($file && $file->isValid()) {
                            // Delete old image if it exists
                            if (isset($optionImages[$index])) {
                                $oldPath = $optionImages[$index];
                                $this->deleteImage( $oldPath);
                            }
                            // Upload new image
                            $path  = $this->uploadImage($file, 'options');;
                            $optionImages[$index] =  $path;
                        }
                    }
                }
            
                if($request->option_images == null){
                      if($question->option_images && count(array_filter($question->option_images)) > 0){
                       foreach ($question->option_images as $index => $oldPath) {
                            if (isset($oldPath)) {
                                $this->deleteImage( $oldPath);
                            }
                         }
                      }
                    $optionImages = null;
                }else{
                // Re-index array to be clean
                  $optionImages = array_values($optionImages);
                }
            }

          // ============ Update the Question ============
            $question->update([
                'subject_id' => $validated['subject_id'],
                'question_type' => $validated['question_type'],
                'question_text' => $validated['question_text'],
                'options' => $validated['question_type'] === 'objective' ? array_values(array_filter($request->input('options', []))) : null,
                'option_images' => $optionImages,
                'correct_answer' => $validated['question_type'] === 'objective' ? $finalCorrectAnswer : null,
                'score' => $validated['score'],
                'difficulty' => $validated['difficulty'],
            ]);

        return redirect()->route('admin.questions')
            ->with('success', 'Question updated successfully.');
    }

    

       private function uploadImage($file, $folder = 'questions')
    {
        return  FuncController::uploadImage($file, $folder);
    }

    private function deleteImage($imagePath){
         FuncController::deleteImage($imagePath);
    }

    // =====================================================
    // DELETE
    // =====================================================
    public function delete($id)
    {
        $question = Question::findOrFail($id);

        // Delete image from wherever it lives
        $this->deleteImage($question->image_path);

        $question->delete();

        return redirect()->route('admin.questions')
            ->with('success', 'Question deleted successfully.');
    }

      public function bulkDelete(Request $request)
        {
            $request->validate([
                'question_ids' => 'required|array',
                'question_ids.*' => 'exists:questions,id',
            ]);

            $questions = Question::whereIn('id', $request->question_ids)->get();

            foreach ($questions as $question) {
                $this->deleteImage($question->image_path);
                $question->delete();
            }

            return redirect()->route('admin.questions')
                ->with('success', $questions->count() . ' questions deleted successfully.');
        }
  

    public function import()
    {
        $subjects = Subject::with('class')->get();
        return view('admin.questions.import', compact('subjects'));
    }

    /**
     * Import questions from CSV file
     */
    public function importCSV(Request $request)
    {
        // Validate request
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

            // Read header
            $header = fgetcsv($handle);
            if (!$header) {
                fclose($handle);
                return back()->with('error', 'The CSV file appears to be empty or invalid.');
            }

            // Clean and normalize header
            $header = array_map(function($col) {
                return strtolower(trim($col));
            }, $header);

            // Validate header
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

                // Map row data to columns
                $data = [];
                foreach ($header as $index => $column) {
                    $data[$column] = trim($row[$index] ?? '');
                }

                // Validate row data
                $rowErrors = $this->validateRow($data, $rowNumber);
                if (!empty($rowErrors)) {
                    $errors = array_merge($errors, $rowErrors);
                    continue;
                }

                // Build options array
                $options = array_values(array_filter([
                    $data['option_a'],
                    $data['option_b'],
                    $data['option_c'],
                    $data['option_d'],
                ]));

                // Determine correct answer
                $correctAnswer = $data['correct_answer'];
                if (!in_array($correctAnswer, $options)) {
                    $correctAnswer = $options[0];
                    $errors[] = "Row {$rowNumber}: Correct answer not in options, using '{$correctAnswer}' as default.";
                }

                // Prepare question data
                $questions[] = [
                    'subject_id' => $request->subject_id,
                    'question_text' => $data['question'],
                    'options' => json_encode($options),
                    'correct_answer' => $correctAnswer,
                    'score' => isset($data['score']) && is_numeric($data['score']) ? (int)$data['score'] : 1,
                    'difficulty' => isset($data['difficulty']) ? strtolower(trim($data['difficulty'])) : 'medium',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $imported++;
            }

            fclose($handle);

            // Bulk insert for better performance
            if (!empty($questions)) {
                Question::insert($questions);
            }

            // Prepare response message
            if ($imported === 0) {
                return back()->with('error', 'No questions were imported. Please check your file format.');
            }

            $message = "{$imported} question(s) imported successfully.";
            if (!empty($errors)) {
                $message .= " Warnings: " . implode('; ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= " and " . (count($errors) - 5) . " more warnings.";
                }
                return redirect()->route('admin.questions')->with('warning', $message);
            }

            return redirect()->route('admin.questions')->with('success', $message);

        } catch (\Exception $e) {
            Log::error('CSV Import Error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            if (isset($handle) && is_resource($handle)) {
                fclose($handle);
            }
            
            return back()->with('error', 'Error importing questions: ' . $e->getMessage());
        }
    }

    /**
     * Validate a single row of data
     */
    private function validateRow($data, $rowNumber)
    {
        $errors = [];

        // Check required fields
        if (empty($data['question'])) {
            $errors[] = "Row {$rowNumber}: Question text is required.";
        }

        if (empty($data['option_a'])) {
            $errors[] = "Row {$rowNumber}: Option A is required.";
        }

        if (empty($data['option_b'])) {
            $errors[] = "Row {$rowNumber}: Option B is required.";
        }

        // Check if we have at least 2 options
        $options = array_filter([
            $data['option_a'] ?? '',
            $data['option_b'] ?? '',
            $data['option_c'] ?? '',
            $data['option_d'] ?? '',
        ]);

        if (count($options) < 2) {
            $errors[] = "Row {$rowNumber}: At least 2 options are required.";
        }

        // Validate difficulty if provided
        if (!empty($data['difficulty'])) {
            $difficulty = strtolower(trim($data['difficulty']));
            if (!in_array($difficulty, ['easy', 'medium', 'hard'])) {
                $errors[] = "Row {$rowNumber}: Difficulty must be 'easy', 'medium', or 'hard'.";
            }
        }

        // Validate score if provided
        if (!empty($data['score']) && !is_numeric($data['score'])) {
            $errors[] = "Row {$rowNumber}: Score must be a number.";
        }

        return $errors;
    }

    /**
     * Download CSV template
     */
    public function downloadTemplate()
    {
        try {
            $headers = ['question', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'score', 'difficulty'];
            $sampleData = [
                ['What is 2+2?', '3', '4', '5', '6', '4', '1', 'easy'],
                ['What is the capital of Nigeria?', 'Lagos', 'Abuja', 'Kano', 'Ibadan', 'Abuja', '2', 'medium'],
                ['Which planet is known as the Red Planet?', 'Venus', 'Mars', 'Jupiter', 'Saturn', 'Mars', '1', 'easy'],
                ['What is the chemical symbol for water?', 'H2O', 'CO2', 'NaCl', 'HCl', 'H2O', '1', 'easy'],
            ];

            $output = fopen('php://memory', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
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
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);

        } catch (\Exception $e) {
            Log::error('Error generating template: ' . $e->getMessage());
            return back()->with('error', 'Error generating template: ' . $e->getMessage());
        }
    }

    /**
     * Export questions as CSV
     */
    public function exportCSV()
    {
        try {
            $questions = Question::with('subject')->get();
            
            $headers = ['id', 'question', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'score', 'difficulty', 'subject'];
            
            $output = fopen('php://memory', 'w');
            fputs($output, "\xEF\xBB\xBF");
            fputcsv($output, $headers);
            
            foreach ($questions as $question) {
                $options = $question->options;
                $row = [
                    $question->id,
                    $question->question_text,
                    $options[0] ?? '',
                    $options[1] ?? '',
                    $options[2] ?? '',
                    $options[3] ?? '',
                    $question->correct_answer,
                    $question->score,
                    $question->difficulty,
                    $question->subject->name ?? 'N/A',
                ];
                fputcsv($output, $row);
            }
            
            fseek($output, 0);
            $content = stream_get_contents($output);
            fclose($output);

            return response($content, 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="questions_export_' . date('Y-m-d') . '.csv"',
            ]);

        } catch (\Exception $e) {
            Log::error('Error exporting CSV: ' . $e->getMessage());
            return back()->with('error', 'Error exporting questions.');
        }
    }

    /**
     * Sample data for testing
     */
    public function sampleCSV()
    {
        return response()->json([
            'headers' => ['question', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'score', 'difficulty'],
            'sample' => [
                ['What is 2+2?', '3', '4', '5', '6', '4', '1', 'easy'],
                ['What is the capital of Nigeria?', 'Lagos', 'Abuja', 'Kano', 'Ibadan', 'Abuja', '2', 'medium'],
            ]
        ]);
    }

       public function importExcel(Request $request)
    {
        try {
            $request->validate([
                'subject_id' => 'required|exists:subjects,id',
                'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            ]);

            Log::info('Starting import for subject: ' . $request->subject_id);
            Log::info('File: ' . $request->file('file')->getClientOriginalName());

            // Create import instance
            $import = new QuestionsImport($request->subject_id);
            
            // Import the file
            Excel::import($import, $request->file('file'));
            
            // Get results
            $importedCount = $import->getImportedCount();
            $errors = $import->getErrors();

            Log::info("Import completed. Imported: {$importedCount}, Errors: " . count($errors));

            // Check for errors
            if ($import->hasErrors()) {
                $errorMessage = implode('; ', array_unique($errors));
                
                if ($importedCount > 0) {
                    return redirect()->route('admin.questions')
                        ->with('warning', "{$importedCount} questions imported successfully, but with warnings: " . $errorMessage);
                } else {
                    return back()->with('error', 'Import failed: ' . $errorMessage);
                }
            }

            if ($importedCount === 0) {
                return back()->with('error', 'No questions were imported. Please check your file format.');
            }

            return redirect()->route('admin.questions')
                ->with('success', "{$importedCount} questions imported successfully.");

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            Log::error('Validation Exception: ' . $e->getMessage());
            
            $failures = $e->failures();
            $errorMessages = [];
            
            foreach ($failures as $failure) {
                $errorMessages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
            }
            
            return back()->with('error', 'Validation errors: ' . implode('; ', $errorMessages));
            
        } catch (\Exception $e) {
            Log::error('Excel Import Error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return back()->with('error', 'Error importing questions: ' . $e->getMessage());
        }
    }

    public function getQuestionsBySubject($subjectId)
    {
        try {
            $questions = Question::where('subject_id', $subjectId)
                                ->get(['id', 'question_text', 'score', 'difficulty', 'question_type']);
            
            return response()->json($questions);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching questions: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load questions'], 500);
        }
    }


}