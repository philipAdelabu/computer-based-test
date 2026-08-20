<!-- resources/views/teacher/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('sidebar')
    @include('teacher.partials.sidebar')
@endsection

@section('page-title', 'Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color: var(--primary-color);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">My Subjects</h6>
                    <h3 class="mb-0">{{ $totalSubjects }}</h3>
                </div>
                <div class="stat-icon text-primary">
                    <i class="bi bi-book"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color: var(--secondary-color);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Total Questions</h6>
                    <h3 class="mb-0">{{ $totalQuestions }}</h3>
                </div>
                <div class="stat-icon text-success">
                    <i class="bi bi-question-circle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color: var(--warning-color);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Total Exams</h6>
                    <h3 class="mb-0">{{ $totalExams }}</h3>
                </div>
                <div class="stat-icon text-warning">
                    <i class="bi bi-file-text"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color: var(--info-color);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">My Students</h6>
                    <h3 class="mb-0">{{ $totalStudents }}</h3>
                </div>
                <div class="stat-icon text-info">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="{{ route('teacher.subjects') }}" class="btn btn-outline-primary w-100 py-3">
                            <i class="bi bi-book fs-4 d-block"></i>
                            View My Subjects
                        </a>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-outline-success w-100 py-3" data-bs-toggle="modal" data-bs-target="#uploadScoreModal">
                            <i class="bi bi-upload fs-4 d-block"></i>
                            Upload Scores
                        </button>
                    </div>
                    <div class="col-md-4">
                        <a href="#" class="btn btn-outline-info w-100 py-3">
                            <i class="bi bi-file-earmark-text fs-4 d-block"></i>
                            Generate Report Cards
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Score Modal -->
<div class="modal fade" id="uploadScoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Student Score</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('teacher.upload-score') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <select name="subject_id" class="form-select" required>
                            <option value="">Select Subject</option>
                            @foreach(\App\Models\Subject::where('teacher_id', auth()->id())->get() as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Student</label>
                        <select name="student_id" class="form-select" required>
                            <option value="">Select Student</option>
                            @foreach(\App\Models\Student::with('user')->get() as $student)
                                <option value="{{ $student->id }}">{{ $student->user->name }} ({{ $student->admission_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Score</label>
                            <input type="number" name="score" class="form-control" required min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Max Score</label>
                            <input type="number" name="max_score" class="form-control" required min="1">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assessment Type</label>
                        <input type="text" name="assessment_type" class="form-control" required placeholder="e.g., Mid-Term Exam">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assessment Date</label>
                        <input type="date" name="assessment_date" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload Score</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection