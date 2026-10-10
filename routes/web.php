<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\ScoreController as AdminScoreController;
use App\Http\Controllers\Admin\ExamController as AdminExamController;
use App\Http\Controllers\Admin\ReportCardController as AdminReportCardController;
use App\Http\Controllers\Admin\StudentAccessController;
use App\Http\Controllers\Teacher\QuestionController as TeacherQuestionController;
use App\Http\Controllers\Teacher\TeacherController;
use App\Http\Controllers\Teacher\ExamController;
use App\Http\Controllers\Teacher\ScoreController;
use App\Http\Controllers\Teacher\ReportCardController;
use App\Http\Controllers\Teacher\StudentAccessController as TeacherStudentAccessController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Middleware\CheckRole;
use App\Http\Controllers\RichEditorController;

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

          // AJAX route for fetching questions by subject
    Route::get('/questions/by-subject/{subjectId}', [QuestionController::class, 'getQuestionsBySubject'])
        ->name('questions.by-subject');

    Route::get('/questions/{id}/edit', [QuestionController::class, 'edit'])->name('questions.edit');
    Route::put('/questions/{id}', [QuestionController::class, 'update'])->name('questions.update');
    Route::delete('/questions/{id}', [QuestionController::class, 'delete'])->name('questions.delete');
    Route::delete('/questions/bulk-delete', [QuestionController::class, 'bulkDelete'])->name('questions.bulk-delete');
    

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

    // ============ EXAM MANAGEMENT ============
    Route::get('/exams', [AdminExamController::class, 'index'])->name('exams');
    Route::get('/exams/create', [AdminExamController::class, 'create'])->name('exams.create');
    Route::post('/exams', [AdminExamController::class, 'store'])->name('exams.store');
    Route::get('/exams/{id}', [AdminExamController::class, 'show'])->name('exams.show');
    Route::get('/exams/{id}/edit', [AdminExamController::class, 'edit'])->name('exams.edit');
    Route::put('/exams/{id}', [AdminExamController::class, 'update'])->name('exams.update');
    Route::delete('/exams/{id}', [AdminExamController::class, 'delete'])->name('exams.delete');
    Route::post('/exams/{id}/publish', [AdminExamController::class, 'publish'])->name('exams.publish');
    Route::post('/exams/{id}/unpublish', [AdminExamController::class, 'unpublish'])->name('exams.unpublish');
    
    // AJAX for exams
    Route::get('/exams/questions/{subjectId}', [AdminExamController::class, 'getQuestions'])->name('exams.questions');
    Route::get('/exams/students/{examId}', [AdminExamController::class, 'getStudents'])->name('exams.students');
    
    // ============ SCORE MANAGEMENT ============
    Route::get('/scores', [AdminScoreController::class, 'index'])->name('scores');
    Route::get('/scores/create', [AdminScoreController::class, 'create'])->name('scores.create');
    Route::post('/scores', [AdminScoreController::class, 'store'])->name('scores.store');
    Route::get('/scores/{id}/edit', [AdminScoreController::class, 'edit'])->name('scores.edit');
    Route::put('/scores/{id}', [AdminScoreController::class, 'update'])->name('scores.update');
    Route::delete('/scores/{id}', [AdminScoreController::class, 'delete'])->name('scores.delete');
    
    // Bulk upload
    Route::get('/scores/bulk-upload', [AdminScoreController::class, 'bulkUpload'])->name('scores.bulk-upload');
    Route::post('/scores/bulk-upload', [AdminScoreController::class, 'bulkUploadStore'])->name('scores.bulk-upload.store');
    
    // AJAX
    Route::get('/scores/students/{classId}', [AdminScoreController::class, 'getStudentsByClass'])->name('scores.students');
    
    // ============ REPORT CARDS ============
    Route::get('/report-cards', [AdminReportCardController::class, 'index'])->name('report-cards.index');
    Route::post('/report-cards/generate', [AdminReportCardController::class, 'generate'])->name('report-cards.generate');
    Route::get('/report-cards/{id}', [AdminReportCardController::class, 'show'])->name('report-cards.show');
    Route::delete('/report-cards/{id}', [AdminReportCardController::class, 'delete'])->name('report-cards.delete');
    Route::post('/report-cards/class-report', [AdminReportCardController::class, 'classReport'])->name('report-cards.class-report');
     // Settings Routes
    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

     // Student Assessment Access
    Route::post('/students/{studentId}/deactivate-assessments', 
        [\App\Http\Controllers\Admin\StudentAccessController::class, 'deactivate'])
        ->name('students.deactivate-assessments');
    
    Route::post('/students/{studentId}/reactivate-assessments', 
        [\App\Http\Controllers\Admin\StudentAccessController::class, 'reactivate'])
        ->name('students.reactivate-assessments');
    
    Route::post('/students/bulk-deactivate', 
        [\App\Http\Controllers\Admin\StudentAccessController::class, 'bulkDeactivate'])
        ->name('students.bulk-deactivate');
    
    Route::post('/students/bulk-reactivate', 
        [\App\Http\Controllers\Admin\StudentAccessController::class, 'bulkReactivate'])
        ->name('students.bulk-reactivate');

    });

// Teacher Routes
Route::middleware(['auth', CheckRole::class . ':teacher'])->prefix('teacher')->name('teacher.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');
    Route::get('/subjects', [TeacherController::class, 'subjects'])->name('subjects');
    Route::post('/upload-score', [TeacherController::class, 'uploadScores'])->name('upload-score');
    Route::get('/report-card/{student}', [TeacherController::class, 'generateReportCard'])->name('report-card');
    
    // Question Management
    Route::get('/questions', [TeacherQuestionController::class, 'index'])->name('questions');
    Route::get('/questions/create', [TeacherQuestionController::class, 'create'])->name('questions.create');
    Route::post('/questions', [TeacherQuestionController::class, 'store'])->name('questions.store');
    Route::get('/questions/{id}/edit', [TeacherQuestionController::class, 'edit'])->name('questions.edit');
    Route::put('/questions/{id}', [TeacherQuestionController::class, 'update'])->name('questions.update');
    Route::delete('/questions/{id}', [TeacherQuestionController::class, 'delete'])->name('questions.delete');
    Route::delete('/questions/bulk-delete', [TeacherQuestionController::class, 'bulkDelete'])->name('questions.bulk-delete');
    
    // Question Import
    Route::get('/questions/import', [TeacherQuestionController::class, 'import'])->name('questions.import');
    Route::post('/questions/import-csv', [TeacherQuestionController::class, 'importCSV'])->name('questions.import.csv');
    Route::get('/questions/download-template', [TeacherQuestionController::class, 'downloadTemplate'])->name('questions.download-template');
    
    // AJAX Routes
    Route::get('/questions/by-subject/{subjectId}', [TeacherQuestionController::class, 'getQuestionsBySubject'])->name('questions.by-subject');
    
    // Exam Management
     // Exam Management
    Route::get('/exams', [ExamController::class, 'index'])->name('exams');
    Route::get('/exams/create', [ExamController::class, 'create'])->name('exams.create');
    Route::post('/exams', [ExamController::class, 'store'])->name('exams.store');
    Route::get('/exams/{id}', [ExamController::class, 'show'])->name('exams.show');
    Route::get('/exams/{id}/edit', [ExamController::class, 'edit'])->name('exams.edit');
    Route::put('/exams/{id}', [ExamController::class, 'update'])->name('exams.update');
    Route::delete('/exams/{id}', [ExamController::class, 'delete'])->name('exams.delete');
    Route::post('/exams/{id}/publish', [ExamController::class, 'publish'])->name('exams.publish');
    Route::post('/exams/{id}/unpublish', [ExamController::class, 'unpublish'])->name('exams.unpublish');
    // AJAX routes for exams
    Route::get('/exams/questions/{subjectId}', [ExamController::class, 'getQuestions'])->name('exams.questions');
    Route::get('/exams/students/{examId}', [ExamController::class, 'getStudents'])->name('exams.students');

    Route::get('/report-cards', [ReportCardController::class, 'index'])->name('report-cards.index');
    Route::post('/report-cards/generate', [ReportCardController::class, 'generate'])->name('report-cards.generate');
    Route::get('/report-cards/{id}', [ReportCardController::class, 'show'])->name('report-cards.show');
    Route::post('/report-cards/class-report', [ReportCardController::class, 'classReport'])->name('report-cards.class-report');
// ============ SCORE MANAGEMENT ============
    Route::get('/scores', [ScoreController::class, 'index'])->name('scores');
    Route::get('/scores/create', [ScoreController::class, 'create'])->name('scores.create');
    Route::post('/scores', [ScoreController::class, 'store'])->name('scores.store');
    Route::get('/scores/{id}/edit', [ScoreController::class, 'edit'])->name('scores.edit');
    Route::put('/scores/{id}', [ScoreController::class, 'update'])->name('scores.update');
    Route::delete('/scores/{id}', [ScoreController::class, 'delete'])->name('scores.delete');
    
    // Bulk upload
    Route::get('/scores/bulk-upload', [ScoreController::class, 'bulkUpload'])->name('scores.bulk-upload');
    Route::post('/scores/bulk-upload', [ScoreController::class, 'bulkUploadStore'])->name('scores.bulk-upload.store');
    
    // AJAX routes
    Route::get('/scores/students/{subjectId}', [ScoreController::class, 'getStudentsBySubject'])->name('scores.students');
    Route::get('/scores/subjects/{classId}', [ScoreController::class, 'getSubjectsByClass'])->name('scores.subjects');

       // Student Assessment Access
    Route::post('/students/{studentId}/deactivate-assessments', 
        [\App\Http\Controllers\Teacher\TeacherStudentAccessController::class, 'deactivate'])
        ->name('students.deactivate-assessments');
    
    Route::post('/students/{studentId}/reactivate-assessments', 
        [\App\Http\Controllers\Teacher\TeacherStudentAccessController::class, 'reactivate'])
        ->name('students.reactivate-assessments');

});

// Student Routes
Route::middleware(['auth', CheckRole::class .':student'])->prefix('student')->name('student.')->group(function () {

    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/exams', [StudentController::class, 'exams'])->name('exams');
    
    // Exam taking routes
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
// routes/web.php - Replace the default home route

Route::get('/', function () {
    // If user is logged in, redirect to their dashboard
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isTeacher()) {
            return redirect()->route('teacher.dashboard');
        } else {
            return redirect()->route('student.dashboard');
        }
    }
    
    return view('welcome');
})->name('home');

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

Route::middleware(['auth'])->group(function () {
    Route::post('/rich-editor/upload-image', [RichEditorController::class, 'uploadImage'])
        ->name('rich-editor.upload-image');
});