<?php

namespace App\Imports;

use App\Models\Question;
use App\Models\Subject;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuestionsImport implements ToModel, WithHeadingRow
{
    protected $subjectId;

    public function __construct($subjectId)
    {
        $this->subjectId = $subjectId;
    }

    public function model(array $row)
    {
        // Expected columns: question, option_a, option_b, option_c, option_d, correct_answer, score
        $options = [
            $row['option_a'],
            $row['option_b'],
            $row['option_c'],
            $row['option_d'],
        ];

        return new Question([
            'subject_id' => $this->subjectId,
            'question_text' => $row['question'],
            'options' => json_encode($options),
            'correct_answer' => $row['correct_answer'],
            'score' => $row['score'] ?? 1,
            'difficulty' => $row['difficulty'] ?? 'medium',
        ]);
    }
}