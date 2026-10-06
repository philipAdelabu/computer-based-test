<!-- resources/views/teacher/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('sidebar')
    @include('teacher.partials.sidebar')
@endsection

@section('page-title', 'Dashboard')

@section('content')
<!-- Welcome Banner -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Welcome back, {{ Auth::user()->name }}!</h4>
                        <p class="mb-0">
                            <i class="bi bi-calendar"></i> 
                            {{ now()->timezone(config('app.timezone'))->format('l, F d, Y') }}
                        </p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-light text-dark fs-6">
                            <i class="bi bi-person-badge"></i> Teacher
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards Row 1 -->
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
        <div class="stat-card" style="border-left-color: var(--info-color);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Question Bank</h6>
                    <h3 class="mb-0">{{ $totalQuestions }}</h3>
                </div>
                <div class="stat-icon text-info">
                    <i class="bi bi-question-circle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color: var(--secondary-color);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Total Students</h6>
                    <h3 class="mb-0">{{ $totalStudents }}</h3>
                </div>
                <div class="stat-icon text-success">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color: var(--warning-color);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Submissions</h6>
                    <h3 class="mb-0">{{ $totalAttempts }}</h3>
                </div>
                <div class="stat-icon text-warning">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test & Exam Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-info me-2">Tests</span>
                            <small class="text-muted">Continuous Assessment</small>
                        </div>
                        <h2 class="mb-0">{{ $totalTests }}</h2>
                        <small class="text-muted">Total tests created</small>
                    </div>
                    <div class="text-end">
                        <i class="bi bi-file-text text-info" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
                <a href="{{ route('teacher.exams') }}?type=test" class="btn btn-sm btn-outline-info mt-3 w-100">
                    <i class="bi bi-eye"></i> View All Tests
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-primary me-2">Exams</span>
                            <small class="text-muted">Final Examinations</small>
                        </div>
                        <h2 class="mb-0">{{ $totalExams }}</h2>
                        <small class="text-muted">Total exams created</small>
                    </div>
                    <div class="text-end">
                        <i class="bi bi-file-earmark-text text-primary" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
                <a href="{{ route('teacher.exams') }}?type=exam" class="btn btn-sm btn-outline-primary mt-3 w-100">
                    <i class="bi bi-eye"></i> View All Exams
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-lightning-charge"></i> Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="{{ route('teacher.questions.create') }}" class="btn btn-outline-primary w-100 py-3">
                            <i class="bi bi-plus-circle fs-4 d-block"></i>
                            Add Question
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('teacher.exams.create') }}?type=test" class="btn btn-outline-info w-100 py-3">
                            <i class="bi bi-file-text fs-4 d-block"></i>
                            Create Test
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('teacher.exams.create') }}?type=exam" class="btn btn-outline-success w-100 py-3">
                            <i class="bi bi-file-earmark-text fs-4 d-block"></i>
                            Create Exam
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('teacher.report-cards.index') }}" class="btn btn-outline-warning w-100 py-3">
                            <i class="bi bi-file-earmark-bar-graph fs-4 d-block"></i>
                            Report Cards
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Active Assessments & Recent Activity -->
<div class="row mb-4">
    <div class="col-md-7">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-play-circle"></i> Active Assessments</h6>
                <span class="badge bg-success">{{ $activeAssessments->count() }}</span>
            </div>
            <div class="card-body p-0">
                @if($activeAssessments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Subject</th>
                                    <th>Duration</th>
                                    <th>Ends</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activeAssessments as $assessment)
                                    <tr>
                                        <td>
                                            <strong>{{ $assessment->title }}</strong>
                                        </td>
                                        <td>
                                            @if($assessment->assessment_type == 'test')
                                                <span class="badge bg-info">Test</span>
                                            @else
                                                <span class="badge bg-primary">Exam</span>
                                            @endif
                                        </td>
                                        <td>{{ $assessment->subject->name }}</td>
                                        <td>{{ $assessment->duration_minutes }} min</td>
                                        <td>
                                            @if($assessment->schedule_type == 'no_date')
                                                <span class="text-success">Always</span>
                                            @elseif($assessment->schedule_type == 'single_date' && $assessment->end_date)
                                                {{ $assessment->end_date->timezone(config('app.timezone'))->format('M d, h:i A') }}
                                            @elseif($assessment->schedule_type == 'date_range' && $assessment->available_to)
                                                {{ $assessment->available_to->timezone(config('app.timezone'))->format('M d, h:i A') }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-inbox fs-1 d-block text-muted"></i>
                        <p class="text-muted mb-0">No active assessments right now.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-clock-history"></i> Recent Submissions</h6>
                <span class="badge bg-info">{{ $recentAttempts->count() }}</span>
            </div>
            <div class="card-body p-0">
                @if($recentAttempts->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentAttempts as $attempt)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>{{ $attempt->student->user->name }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            {{ $attempt->exam->title }}
                                            <span class="badge 
                                                @if($attempt->exam->assessment_type == 'test') bg-info
                                                @else bg-primary
                                                @endif" 
                                                style="font-size: 0.6rem;">
                                                {{ ucfirst($attempt->exam->assessment_type) }}
                                            </span>
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge 
                                            @if($attempt->score >= $attempt->exam->total_score * 0.7) bg-success
                                            @elseif($attempt->score >= $attempt->exam->total_score * 0.5) bg-warning
                                            @else bg-danger
                                            @endif">
                                            {{ $attempt->score }}/{{ $attempt->exam->total_score }}
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            {{ $attempt->completed_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-inbox fs-1 d-block text-muted"></i>
                        <p class="text-muted mb-0">No recent submissions.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Subject Performance & Top Students -->
<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-graph-up"></i> Subject Performance</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Class</th>
                                <th class="text-center">Tests</th>
                                <th class="text-center">Exams</th>
                                <th class="text-center">Questions</th>
                                <th class="text-center">Submissions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subjectPerformance as $perf)
                                <tr>
                                    <td><strong>{{ $perf['subject']->name }}</strong></td>
                                    <td>{{ $perf['subject']->class->name ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $perf['tests'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $perf['exams'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $perf['questions'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ $perf['attempts'] }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-3">
                                        <p class="text-muted mb-0">No subjects assigned yet.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-trophy"></i> Top Performing Students</h6>
            </div>
            <div class="card-body p-0">
                @if($topStudents->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($topStudents as $index => $student)
                            <div class="list-group-item">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        @if($index == 0)
                                            <i class="bi bi-trophy-fill text-warning fs-3"></i>
                                        @elseif($index == 1)
                                            <i class="bi bi-trophy-fill text-secondary fs-3"></i>
                                        @elseif($index == 2)
                                            <i class="bi bi-trophy-fill text-danger fs-3"></i>
                                        @else
                                            <span class="badge bg-secondary">{{ $index + 1 }}</span>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <strong>{{ $student->user->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $student->admission_number }}</small>
                                    </div>
                                    <div>
                                        <span class="badge bg-success">
                                            {{ number_format($student->average_score ?? 0, 1) }}%
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-people fs-1 d-block text-muted"></i>
                        <p class="text-muted mb-0">No student data available.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection