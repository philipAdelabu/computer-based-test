<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Question;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Imports\QuestionsImport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
}