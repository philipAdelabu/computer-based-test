<!-- resources/views/student/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('sidebar')
    @include('student.partials.sidebar')
@endsection

@section('page-title', 'Dashboard')

@section('content')
@php
    $student = Auth::user()->student;
    $class = $student->class;
    $subjects = $class ? $class->subjects : collect();
@endphp

<!-- Student Info Card -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-1">Welcome back, {{ Auth::user()->name }}!</h4>
                        <p class="mb-0">
                            <i class="bi bi-mortarboard"></i> 
                            <strong>Class:</strong> {{ $class ? $class->name : 'Not Assigned' }} | 
                            <strong>Admission #:</strong> {{ $student->admission_number }}
                        </p>
                        @if($class)
                            <p class="mb-0 mt-1">
                                <i class="bi bi-book"></i> 
                                <strong>Subjects:</strong> {{ $subjects->count() }} subjects
                            </p>
                        @endif
                    </div>
                    <div class="col-md-4 text-md-end">
                        <span class="badge bg-light text-dark fs-6">
                            <i class="bi bi-calendar"></i> 
                            {{ now()->timezone(config('app.timezone'))->format('F d, Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color: var(--primary-color);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Available Exams</h6>
                    <h3 class="mb-0">{{ $availableExams }}</h3>
                </div>
                <div class="stat-icon text-primary">
                    <i class="bi bi-file-text"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color: var(--secondary-color);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Upcoming Exams</h6>
                    <h3 class="mb-0">{{ $upcomingExams }}</h3>
                </div>
                <div class="stat-icon text-success">
                    <i class="bi bi-clock"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color: var(--warning-color);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Completed Exams</h6>
                    <h3 class="mb-0">{{ $completedExams }}</h3>
                </div>
                <div class="stat-icon text-warning">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color: var(--info-color);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Average Score</h6>
                    <h3 class="mb-0">{{ number_format($averageScore, 1) }}%</h3>
                </div>
                <div class="stat-icon text-info">
                    <i class="bi bi-graph-up"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- My Subjects -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-book"></i> My Subjects</h6>
            </div>
            <div class="card-body">
                @if($subjects->count() > 0)
                    <div class="row g-3">
                        @foreach($subjects as $subject)
                            <div class="col-md-3">
                                <div class="border rounded p-3 text-center subject-card">
                                    <div class="mb-2">
                                        <span class="badge bg-primary">{{ $subject->code }}</span>
                                    </div>
                                    <h6 class="mb-1">{{ $subject->name }}</h6>
                                    <small class="text-muted">Teacher: {{ $subject->teacher->name ?? 'N/A' }}</small>
                                    <div class="mt-2">
                                        <span class="badge bg-secondary">{{ $subject->questions->count() }} Questions</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="bi bi-book fs-1 d-block text-muted"></i>
                        <p class="text-muted">No subjects assigned to your class yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Available Exams -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-file-text"></i> Available Exams</h6>
                <a href="{{ route('student.exams') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                @if($availableExamsList->count() > 0)
                    <div class="row g-3">
                        @foreach($availableExamsList as $exam)
                            <div class="col-md-4">
                                <div class="exam-card">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="exam-title">{{ $exam->title }}</div>
                                        <span class="badge bg-success">Available</span>
                                    </div>
                                    <div class="exam-meta">
                                        <div><i class="bi bi-book"></i> {{ $exam->subject->name }}</div>
                                        <div><i class="bi bi-clock"></i> {{ $exam->duration_minutes }} minutes</div>
                                        <div><i class="bi bi-question-circle"></i> {{ $exam->total_questions }} questions</div>
                                        @if($exam->schedule_type == 'date_range')
                                            <div><i class="bi bi-calendar-range"></i> 
                                                {{ $exam->available_from ? $exam->available_from->timezone(config('app.timezone'))->format('M d, Y h:i A') : 'N/A' }}
                                            </div>
                                        @elseif($exam->schedule_type == 'single_date')
                                            <div><i class="bi bi-calendar"></i> {{ $exam->formatted_start_date }}</div>
                                        @else
                                            <div><i class="bi bi-infinity"></i> Always Available</div>
                                        @endif
                                    </div>
                                    @php
                                        $attempt = $exam->attempts->first();
                                    @endphp
                                    @if($attempt && $attempt->status === 'in_progress')
                                        <a href="{{ route('student.exam.continue', $attempt->id) }}" 
                                           class="btn btn-warning w-100 mt-3">
                                            <i class="bi bi-play-circle"></i> Continue Exam
                                        </a>
                                    @elseif($attempt && $attempt->status === 'submitted')
                                        <a href="{{ route('student.exam.result', $attempt->id) }}" 
                                           class="btn btn-success w-100 mt-3">
                                            <i class="bi bi-check-circle"></i> View Result
                                        </a>
                                    @else
                                        <a href="{{ route('student.exam.take', $exam->id) }}" 
                                           class="btn btn-primary w-100 mt-3">
                                            <i class="bi bi-play-circle"></i> Start Exam
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="bi bi-file-text fs-1 d-block text-muted"></i>
                        <p class="text-muted">No exams available at the moment.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Results & Quick Actions -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-bar-chart"></i> Recent Results</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Score</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentResults as $result)
                                <tr>
                                    <td>{{ $result->subject->name }}</td>
                                    <td>{{ $result->score }}/{{ $result->max_score }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($result->percentage >= 80) bg-success
                                            @elseif($result->percentage >= 60) bg-warning
                                            @else bg-danger
                                            @endif">
                                            {{ $result->grade }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-3">
                                        <p class="text-muted mb-0">No results available yet.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-speedometer2"></i> Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    @if($availableExams > 0)
                        <a href="{{ route('student.exams') }}" class="btn btn-primary py-3">
                            <i class="bi bi-play-circle me-2"></i> Take Exam
                            <span class="badge bg-light text-dark ms-2">{{ $availableExams }}</span>
                        </a>
                    @else
                        <button class="btn btn-secondary py-3" disabled>
                            <i class="bi bi-play-circle me-2"></i> No Exams Available
                        </button>
                    @endif
                    <a href="{{ route('student.results') }}" class="btn btn-outline-primary py-3">
                        <i class="bi bi-bar-chart me-2"></i> View Results
                    </a>
                    <a href="{{ route('student.report-cards') }}" class="btn btn-outline-success py-3">
                        <i class="bi bi-file-earmark-text me-2"></i> Report Cards
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.subject-card {
    transition: all 0.3s;
}
.subject-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.08);
}
</style>
@endpush