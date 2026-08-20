<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@cbt.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Teacher
        $teacher = User::create([
            'name' => 'Teacher User',
            'email' => 'teacher@cbt.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        // Create Class
        $class = ClassModel::create([
            'name' => 'SSS 1',
            'code' => 'SSS001',
            'description' => 'Senior Secondary School 1',
        ]);

        // Create Subject
        Subject::create([
            'name' => 'Mathematics',
            'code' => 'MATH101',
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'description' => 'Basic Mathematics',
        ]);

        // Create Student
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
        ]);
    }
}