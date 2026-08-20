<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Teacher\TeacherController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Middleware\CheckRole;

// Auth Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Admin Routes
Route::middleware(['auth', CheckRole::class .':admin'])->prefix('admin')->name('admin.')->group(function () {
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

                // Student Admission Routes
        Route::get('/classes/admit', [AdminController::class, 'admitStudents'])->name('classes.admit');
        Route::post('/classes/admit', [AdminController::class, 'processAdmission'])->name('classes.admit.process');

    // Question Management
    Route::get('/questions', [QuestionController::class, 'index'])->name('questions');
    Route::get('/questions/create', [QuestionController::class, 'create'])->name('questions.create');
    Route::post('/questions', [QuestionController::class, 'store'])->name('questions.store');
    Route::get('/questions/{id}/edit', [QuestionController::class, 'edit'])->name('questions.edit');
    Route::put('/questions/{id}', [QuestionController::class, 'update'])->name('questions.update');
    Route::delete('/questions/{id}', [QuestionController::class, 'delete'])->name('questions.delete');
    

    Route::post('/questions/import', [QuestionController::class, 'importExcel'])->name('questions.import.process');
  // CSV Import/Export Routes
    Route::get('/questions/import', [QuestionController::class, 'import'])->name('questions.import');
    Route::post('/questions/import-csv', [QuestionController::class, 'importCSV'])->name('questions.import.csv');
    Route::get('/questions/download-template', [QuestionController::class, 'downloadTemplate'])->name('questions.download-template');
    Route::get('/questions/export-csv', [QuestionController::class, 'exportCSV'])->name('questions.export.csv');
    Route::get('/questions/sample', [QuestionController::class, 'sampleCSV'])->name('questions.sample');

// Subject Management Routes
Route::get('/subjects', [AdminController::class, 'subjects'])->name('subjects');
Route::get('/subjects/create', [AdminController::class, 'createSubject'])->name('subjects.create');
Route::post('/subjects', [AdminController::class, 'storeSubject'])->name('subjects.store');
Route::get('/subjects/{id}/edit', [AdminController::class, 'editSubject'])->name('subjects.edit');
Route::put('/subjects/{id}', [AdminController::class, 'updateSubject'])->name('subjects.update');
Route::delete('/subjects/{id}', [AdminController::class, 'deleteSubject'])->name('subjects.delete');

// Assignment Routes
Route::get('/subjects/assign', [AdminController::class, 'assignSubjectForm'])->name('subjects.assign');
Route::post('/subjects/assign', [AdminController::class, 'assignSubject'])->name('subjects.assign.process');
Route::get('/subjects/bulk-assign', [AdminController::class, 'bulkAssignForm'])->name('subjects.bulk-assign');
Route::post('/subjects/bulk-assign', [AdminController::class, 'bulkAssign'])->name('subjects.bulk-assign.process');

// AJAX Routes for dynamic loading
Route::get('/subjects/by-class/{classId}', [AdminController::class, 'getSubjectsByClass'])->name('subjects.by-class');
Route::get('/subjects/by-teacher/{teacherId}', [AdminController::class, 'getSubjectsByTeacher'])->name('subjects.by-teacher');

// Stats Route
Route::get('/subjects/stats', [AdminController::class, 'subjectStats'])->name('subjects.stats');
   // AJAX route for getting students by class
Route::get('/students/by-class/{classId}', [AdminController::class, 'getStudentsByClass'])->name('students.by-class');

});

// Teacher Routes
Route::middleware(['auth', CheckRole::class . ':teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');
    Route::get('/subjects', [TeacherController::class, 'subjects'])->name('subjects');
    Route::post('/upload-score', [TeacherController::class, 'uploadScores'])->name('upload-score');
    Route::get('/report-card/{student}', [TeacherController::class, 'generateReportCard'])->name('report-card');
});

// Student Routes
Route::middleware(['auth', CheckRole::class .':student'])->prefix('student')->name('student.')->group(function () {
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

// routes/web.php - Add for debugging
Route::get('/test-excel', function() {
    try {
        // Check if Excel facade works
        $class = get_class(\Maatwebsite\Excel\Facades\Excel::class);
        return response()->json([
            'status' => 'success',
            'message' => 'Excel facade is working',
            'class' => $class
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
});