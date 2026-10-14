<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GradingScale;

class GradingScaleSeeder extends Seeder
{
    public function run()
    {
        // Skip if any grading scale exists
        if (GradingScale::count() > 0) {
            return;
        }

        $scale = GradingScale::create([
            'name' => 'Standard Grading Scale',
            'description' => 'Default A-F grading system',
            'is_default' => true,
            'is_active' => true,
        ]);

        $grades = [
            ['grade' => 'A', 'min_score' => 80, 'max_score' => 100, 'remark' => 'Excellent', 'color' => 'success'],
            ['grade' => 'B', 'min_score' => 70, 'max_score' => 79.99, 'remark' => 'Very Good', 'color' => 'primary'],
            ['grade' => 'C', 'min_score' => 60, 'max_score' => 69.99, 'remark' => 'Good', 'color' => 'info'],
            ['grade' => 'D', 'min_score' => 50, 'max_score' => 59.99, 'remark' => 'Fair', 'color' => 'warning'],
            ['grade' => 'E', 'min_score' => 40, 'max_score' => 49.99, 'remark' => 'Poor', 'color' => 'warning'],
            ['grade' => 'F', 'min_score' => 0, 'max_score' => 39.99, 'remark' => 'Very Poor', 'color' => 'danger'],
        ];

        foreach ($grades as $index => $grade) {
            $scale->items()->create(array_merge($grade, ['order' => $index]));
        }
    }
}