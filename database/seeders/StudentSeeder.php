<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Subject;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run()
    {
        // Create a class
        $class = ClassModel::create([
            'name' => 'SSS 1',
            'code' => 'SSS001',
            'description' => 'Senior Secondary School 1',
        ]);

        // Create a teacher
        $teacher = User::create([
            'name' => 'Teacher User',
            'email' => 'teacher@cbt.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        // Create subjects for the class
        $subjects = [
            ['name' => 'Mathematics', 'code' => 'MATH101'],
            ['name' => 'English', 'code' => 'ENG101'],
            ['name' => 'Physics', 'code' => 'PHY101'],
            ['name' => 'Chemistry', 'code' => 'CHEM101'],
        ];

        foreach ($subjects as $subject) {
            Subject::create([
                'name' => $subject['name'],
                'code' => $subject['code'],
                'class_id' => $class->id,
                'teacher_id' => $teacher->id,
            ]);
        }

        // Create a student
        $studentUser = User::create([
            'name' => 'Student User',
            'email' => 'student@cbt.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        Student::create([
            'user_id' => $studentUser->id,
            'class_id' => $class->id,
            'admission_number' => 'STU001',
            'date_of_birth' => '2000-01-01',
            'guardian_name' => 'Guardian Name',
            'guardian_phone' => '08012345678',
            'status' => 'active',
        ]);
    }
}