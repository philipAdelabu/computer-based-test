<?php

namespace App\Imports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\Log;

class QuestionsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use Importable;

    protected $subjectId;
    protected $importedCount = 0;
    protected $errors = [];
    protected $failures = [];

    public function __construct($subjectId)
    {
        $this->subjectId = $subjectId;
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Log the row for debugging
        Log::info('Processing row: ', $row);

        // Build options array
        $options = [
            trim($row['option_a'] ?? ''),
            trim($row['option_b'] ?? ''),
            trim($row['option_c'] ?? ''),
            trim($row['option_d'] ?? ''),
        ];

        // Remove empty options and reindex
        $options = array_values(array_filter($options, function($value) {
            return $value !== '';
        }));

        // Validate minimum options
        if (count($options) < 2) {
            $this->failures[] = [
                'row' => $this->importedCount + 2,
                'error' => 'Question must have at least 2 options.'
            ];
            return null;
        }

        // Validate correct answer
        $correctAnswer = trim($row['correct_answer'] ?? '');
        if (!in_array($correctAnswer, $options)) {
            $this->failures[] = [
                'row' => $this->importedCount + 2,
                'error' => 'Correct answer must match one of the options.'
            ];
            return null;
        }

        $this->importedCount++;

        return new Question([
            'subject_id' => $this->subjectId,
            'question_text' => trim($row['question']),
            'options' => $options,
            'correct_answer' => $correctAnswer,
            'score' => isset($row['score']) ? (int)$row['score'] : 1,
            'difficulty' => isset($row['difficulty']) ? strtolower(trim($row['difficulty'])) : 'medium',
        ]);
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'question' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|string',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'question.required' => 'The question field is required.',
            'option_a.required' => 'Option A is required.',
            'option_b.required' => 'Option B is required.',
            'option_c.required' => 'Option C is required.',
            'option_d.required' => 'Option D is required.',
            'correct_answer.required' => 'The correct answer is required.',
        ];
    }

    /**
     * Handle errors (for exceptions)
     */
    public function onError(\Throwable $e)
    {
        Log::error('Import Error: ' . $e->getMessage());
        $this->errors[] = $e->getMessage();
    }

    /**
     * Handle failures (for validation)
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->failures[] = [
                'row' => $failure->row(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ];
        }
    }

    /**
     * Get imported count
     */
    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    /**
     * Get all failures
     */
    public function getFailures(): array
    {
        return $this->failures;
    }

    /**
     * Get all errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Check if there are failures
     */
    public function hasFailures(): bool
    {
        return !empty($this->failures);
    }

    /**
     * Check if there are errors
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
}