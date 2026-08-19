<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Teacher\TeacherController;
use App\Http\Controllers\Student\StudentController;

// Auth Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Teacher Management
    Route::get('/teachers', [AdminController::class, 'teachers'])->name('teachers');
    Route::get('/teachers/create', [AdminController::class, 'createTeacher'])->name('teachers.create');
    Route::post('/teachers', [AdminController::class, 'storeTeacher'])->name('teachers.store');
    Route::get('/teachers/{id}/edit', [AdminController::class, 'editTeacher'])->name('teachers.edit');
    Route::put('/teachers/{id}', [AdminController::class, 'updateTeacher'])->name('teachers.update');
    Route::delete('/teachers/{id}', [AdminController::class, 'deleteTeacher'])->name('teachers.delete');
    
    // Student Management
    Route::get('/students', [AdminController::class, 'students'])->name('students');
    Route::get('/students/create', [AdminController::class, 'createStudent'])->name('students.create');
    Route::post('/students', [AdminController::class, 'storeStudent'])->name('students.store');
    Route::get('/students/{id}/edit', [AdminController::class, 'editStudent'])->name('students.edit');
    Route::put('/students/{id}', [AdminController::class, 'updateStudent'])->name('students.update');
    Route::delete('/students/{id}', [AdminController::class, 'deleteStudent'])->name('students.delete');
    
    // Class Management
    Route::get('/classes', [AdminController::class, 'classes'])->name('classes');
    Route::get('/classes/create', [AdminController::class, 'createClass'])->name('classes.create');
    Route::post('/classes', [AdminController::class, 'storeClass'])->name('classes.store');
    Route::get('/classes/{id}/edit', [AdminController::class, 'editClass'])->name('classes.edit');
    Route::put('/classes/{id}', [AdminController::class, 'updateClass'])->name('classes.update');
    Route::delete('/classes/{id}', [AdminController::class, 'deleteClass'])->name('classes.delete');
    Route::get('/classes/{id}/admit', [AdminController::class, 'admitStudents'])->name('classes.admit');
    Route::post('/classes/{id}/admit', [AdminController::class, 'processAdmission'])->name('classes.admit.process');
    
    // Question Management
    Route::get('/questions', [QuestionController::class, 'index'])->name('questions');
    Route::get('/questions/create', [QuestionController::class, 'create'])->name('questions.create');
    Route::post('/questions', [QuestionController::class, 'store'])->name('questions.store');
    Route::get('/questions/{id}/edit', [QuestionController::class, 'edit'])->name('questions.edit');
    Route::put('/questions/{id}', [QuestionController::class, 'update'])->name('questions.update');
    Route::delete('/questions/{id}', [QuestionController::class, 'delete'])->name('questions.delete');
    Route::get('/questions/import', [QuestionController::class, 'import'])->name('questions.import');
    Route::post('/questions/import', [QuestionController::class, 'importExcel'])->name('questions.import.process');
});

// Teacher Routes
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');
    Route::get('/subjects', [TeacherController::class, 'subjects'])->name('subjects');
    Route::post('/upload-score', [TeacherController::class, 'uploadScores'])->name('upload-score');
    Route::get('/report-card/{student}', [TeacherController::class, 'generateReportCard'])->name('report-card');
});

// Student Routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/exams', [StudentController::class, 'exams'])->name('exams');
    Route::get('/exam/take/{exam}', [StudentController::class, 'takeExam'])->name('exam.take');
    Route::get('/exam/continue/{attempt}', [StudentController::class, 'continueExam'])->name('exam.continue');
    Route::post('/exam/submit/{attempt}', [StudentController::class, 'submitExam'])->name('exam.submit');
    Route::get('/exam/result/{attempt}', [StudentController::class, 'examResult'])->name('exam.result');
    Route::post('/exam/save-answer/{attempt}', [StudentController::class, 'saveAnswer'])->name('exam.save-answer');
    Route::get('/results', [StudentController::class, 'results'])->name('results');
    Route::get('/report-cards', [StudentController::class, 'reportCards'])->name('report-cards');
    Route::get('/report-card/{id}', [StudentController::class, 'viewReportCard'])->name('report-card.view');
});

// Home Route
Route::get('/', function () {
    return redirect()->route('login');
});