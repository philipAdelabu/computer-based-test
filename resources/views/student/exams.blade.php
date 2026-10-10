<!-- resources/views/student/exams.blade.php -->
@extends('layouts.app')

@section('title', 'My Tests & Exams')

@section('sidebar')
    @include('student.partials.sidebar')
@endsection

@section('page-title', 'Tests & Exams')

@php
    $student = Auth::user()->student;
    $studentCanTake = $student->is_assessment_active;
@endphp

@section('content')
<!-- Info Banner -->
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <div class="d-flex align-items-center">
        <i class="bi bi-info-circle-fill fs-4 me-2"></i>
        <div>
            <strong>Assessment Information</strong>
            <div class="small">
                <span class="badge bg-info me-1">Test</span> Continuous Assessment (CA) — typically worth 30 marks
                <span class="badge bg-primary ms-3 me-1">Exam</span> Final Examination — typically worth 70 marks
            </div>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<!-- Summary Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color: #0dcaf0;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Available Tests</h6>
                    <h3 class="mb-0">{{ $availableTests->count() }}</h3>
                    <small class="text-muted">Continuous Assessment</small>
                </div>
                <div class="stat-icon text-info">
                    <i class="bi bi-file-text"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color: #0d6efd;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Available Exams</h6>
                    <h3 class="mb-0">{{ $availableExams->count() }}</h3>
                    <small class="text-muted">Final Examinations</small>
                </div>
                <div class="stat-icon text-primary">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color: #ffc107;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Upcoming</h6>
                    <h3 class="mb-0">{{ $upcomingAssessments->count() }}</h3>
                    <small class="text-muted">Scheduled</small>
                </div>
                <div class="stat-icon text-warning">
                    <i class="bi bi-clock"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color: #198754;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Completed</h6>
                    <h3 class="mb-0">{{ $completedExams->total() }}</h3>
                    <small class="text-muted">All Time</small>
                </div>
                <div class="stat-icon text-success">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- AVAILABLE TESTS (Continuous Assessment)       -->
<!-- ============================================== -->
@if($availableTests->count() > 0)
    <div class="card border-info mb-4">
        <div class="card-header bg-info bg-opacity-10 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <span class="badge bg-info me-2">
                    <i class="bi bi-file-text"></i> TESTS
                </span>
                Available Tests — Continuous Assessment
            </h6>
            <span class="badge bg-info">{{ $availableTests->count() }}</span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @foreach($availableTests as $test)
                    @php
                        $attempt = $test->attempts->first();
                        $attemptsCount = $test->attempts->count();
                        $hasActiveAttempt = $attempt && $attempt->status === 'in_progress';
                        $hasSubmitted = $attempt && $attempt->status === 'submitted';
                    @endphp
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 assessment-card assessment-test">
                            <div class="card-body">
                                <!-- Header -->
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-info">
                                        <i class="bi bi-file-text"></i> Test
                                    </span>
                                    @if($hasActiveAttempt)
                                        <span class="badge bg-warning">In Progress</span>
                                    @elseif($hasSubmitted)
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge bg-success">Available</span>
                                    @endif
                                </div>
                                
                                <!-- Title & Subject -->
                                <h6 class="fw-bold mb-1">{{ $test->title }}</h6>
                                <p class="text-muted small mb-3">
                                    <i class="bi bi-book"></i> {{ $test->subject->name }}
                                </p>
                                
                                <!-- Info Grid -->
                                <div class="row g-2 mb-3 small">
                                    <div class="col-6">
                                        <div class="p-2 bg-light rounded">
                                            <i class="bi bi-clock text-info"></i>
                                            <strong>{{ $test->duration_minutes }}</strong> min
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 bg-light rounded">
                                            <i class="bi bi-question-circle text-info"></i>
                                            <strong>{{ $test->total_questions }}</strong> Qs
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 bg-light rounded">
                                            <i class="bi bi-award text-info"></i>
                                            <strong>{{ $test->max_marks }}</strong> marks
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 bg-light rounded">
                                            <i class="bi bi-target text-info"></i>
                                            <strong>{{ $test->benchmark }}%</strong> to pass
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Schedule Info -->
                                <div class="mb-3">
                                    @if($test->schedule_type == 'no_date')
                                        <small class="text-muted">
                                            <i class="bi bi-infinity"></i> Always available
                                        </small>
                                    @elseif($test->schedule_type == 'single_date')
                                        <small class="text-muted d-block">
                                            <i class="bi bi-calendar-event"></i> 
                                            {{ $test->start_date ? $test->start_date->timezone(config('app.timezone'))->format('M d, Y h:i A') : 'N/A' }}
                                        </small>
                                    @elseif($test->schedule_type == 'date_range')
                                        <small class="text-muted d-block">
                                            <i class="bi bi-calendar-range"></i> 
                                            {{ $test->available_from ? $test->available_from->timezone(config('app.timezone'))->format('M d, h:i A') : 'N/A' }}
                                            <br>
                                            <i class="bi bi-arrow-right"></i> 
                                            {{ $test->available_to ? $test->available_to->timezone(config('app.timezone'))->format('M d, h:i A') : 'N/A' }}
                                        </small>
                                    @endif
                                    
                                    @if($test->instructions)
                                        <div class="mt-2 p-2 bg-light rounded small">
                                            <i class="bi bi-info-circle text-info"></i>
                                            {{ Str::limit($test->instructions, 80) }}
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Attempt info -->
                                @if($test->max_attempts > 1)
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="bi bi-arrow-repeat"></i>
                                            Attempt {{ $attemptsCount }} of {{ $test->max_attempts }}
                                        </small>
                                    </div>
                                @endif
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                @if($hasActiveAttempt)
                                    <a href="{{ route('student.exam.continue', $attempt->id) }}" 
                                       class="btn btn-warning w-100">
                                        <i class="bi bi-play-circle"></i> Continue Test
                                    </a>
                                @elseif($hasSubmitted)
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('student.exam.result', $attempt->id) }}" 
                                           class="btn btn-success">
                                            <i class="bi bi-check-circle"></i> View Result
                                        </a>
                                        @if($test->max_attempts > 1 && $attemptsCount < $test->max_attempts)
                                            <a href="{{ route('student.exam.take', $test->id) }}" 
                                               class="btn btn-outline-info btn-sm">
                                                <i class="bi bi-arrow-repeat"></i> Retake Test
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <a href="{{ route('student.exam.take', $test->id) }}" 
                                       class="btn btn-info w-100">
                                        <i class="bi bi-play-circle"></i> Start Test
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

<!-- ============================================== -->
<!-- AVAILABLE EXAMS (Final Examination)            -->
<!-- ============================================== -->
@if($availableExams->count() > 0)
    <div class="card border-primary mb-4">
        <div class="card-header bg-primary bg-opacity-10 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <span class="badge bg-primary me-2">
                    <i class="bi bi-file-earmark-text"></i> EXAMS
                </span>
                Available Exams — Final Examination
            </h6>
            <span class="badge bg-primary">{{ $availableExams->count() }}</span>
        </div>
        <div class="card-body">
          
            <div class="row g-3">
                @foreach($availableExams as $exam)
                    @php
                        $attempt = $exam->attempts->first();
                        $attemptsCount = $exam->attempts->count();
                        $hasActiveAttempt = $attempt && $attempt->status === 'in_progress';
                        $hasSubmitted = $attempt && $attempt->status === 'submitted';
                    @endphp
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 assessment-card assessment-exam">
                            <div class="card-body">
                                <!-- Header -->
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-primary">
                                        <i class="bi bi-file-earmark-text"></i> Exam
                                    </span>
                                    @if($hasActiveAttempt)
                                        <span class="badge bg-warning">In Progress</span>
                                    @elseif($hasSubmitted)
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge bg-success">Available</span>
                                    @endif
                                </div>
                                
                                <!-- Title & Subject -->
                                <h6 class="fw-bold mb-1">{{ $exam->title }}</h6>
                                <p class="text-muted small mb-3">
                                    <i class="bi bi-book"></i> {{ $exam->subject->name }}
                                </p>
                                
                                <!-- Info Grid -->
                                <div class="row g-2 mb-3 small">
                                    <div class="col-6">
                                        <div class="p-2 bg-light rounded">
                                            <i class="bi bi-clock text-primary"></i>
                                            <strong>{{ $exam->duration_minutes }}</strong> min
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 bg-light rounded">
                                            <i class="bi bi-question-circle text-primary"></i>
                                            <strong>{{ $exam->total_questions }}</strong> Qs
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 bg-light rounded">
                                            <i class="bi bi-award text-primary"></i>
                                            <strong>{{ $exam->max_marks }}</strong> marks
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 bg-light rounded">
                                            <i class="bi bi-target text-primary"></i>
                                            <strong>{{ $exam->benchmark }}%</strong> to pass
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Schedule Info -->
                                <div class="mb-3">
                                    @if($exam->schedule_type == 'no_date')
                                        <small class="text-muted">
                                            <i class="bi bi-infinity"></i> Always available
                                        </small>
                                    @elseif($exam->schedule_type == 'single_date')
                                        <small class="text-muted d-block">
                                            <i class="bi bi-calendar-event"></i> 
                                            {{ $exam->start_date ? $exam->start_date->timezone(config('app.timezone'))->format('M d, Y h:i A') : 'N/A' }}
                                        </small>
                                    @elseif($exam->schedule_type == 'date_range')
                                        <small class="text-muted d-block">
                                            <i class="bi bi-calendar-range"></i> 
                                            {{ $exam->available_from ? $exam->available_from->timezone(config('app.timezone'))->format('M d, h:i A') : 'N/A' }}
                                            <br>
                                            <i class="bi bi-arrow-right"></i> 
                                            {{ $exam->available_to ? $exam->available_to->timezone(config('app.timezone'))->format('M d, h:i A') : 'N/A' }}
                                        </small>
                                    @endif
                                    
                                    @if($exam->instructions)
                                        <div class="mt-2 p-2 bg-light rounded small">
                                            <i class="bi bi-info-circle text-primary"></i>
                                            {{ Str::limit($exam->instructions, 80) }}
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Attempt info -->
                                @if($exam->max_attempts > 1)
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="bi bi-arrow-repeat"></i>
                                            Attempt {{ $attemptsCount }} of {{ $exam->max_attempts }}
                                        </small>
                                    </div>
                                @endif
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                  <!-- In the exam card -->
                                @if(!$studentCanTake)
                                    <button class="btn btn-secondary w-100 mt-3" disabled>
                                        <i class="bi bi-shield-x"></i> Access Deactivated
                                    </button>
                                @else

                                        @if($hasActiveAttempt)
                                            <a href="{{ route('student.exam.continue', $attempt->id) }}" 
                                            class="btn btn-warning w-100">
                                                <i class="bi bi-play-circle"></i> Continue Exam
                                            </a>
                                        @elseif($hasSubmitted)
                                            <div class="d-grid gap-2">
                                                <a href="{{ route('student.exam.result', $attempt->id) }}" 
                                                class="btn btn-success">
                                                    <i class="bi bi-check-circle"></i> View Result
                                                </a>
                                                @if($exam->max_attempts > 1 && $attemptsCount < $exam->max_attempts)
                                                    <a href="{{ route('student.exam.take', $exam->id) }}" 
                                                    class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-arrow-repeat"></i> Retake Exam
                                                    </a>
                                                @endif
                                            </div>
                                        @else
                                            <a href="{{ route('student.exam.take', $exam->id) }}" 
                                            class="btn btn-primary w-100">
                                                <i class="bi bi-play-circle"></i> Start Exam
                                            </a>
                                        @endif
                                 @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

<!-- No Available Assessments -->
@if($availableTests->count() == 0 && $availableExams->count() == 0)
    <div class="card mb-4">
        <div class="card-body text-center py-5">
            <i class="bi bi-inbox fs-1 d-block text-muted mb-3"></i>
            <h5>No Assessments Available</h5>
            <p class="text-muted mb-0">
                There are no tests or exams available for you at the moment.
            </p>
        </div>
    </div>
@endif

<!-- ============================================== -->
<!-- UPCOMING ASSESSMENTS                           -->
<!-- ============================================== -->
@if($upcomingAssessments->count() > 0)
    <div class="card mb-4">
        <div class="card-header bg-warning bg-opacity-10 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="bi bi-calendar-event text-warning"></i>
                Upcoming Assessments
            </h6>
            <span class="badge bg-warning">{{ $upcomingAssessments->count() }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Title</th>
                            <th>Subject</th>
                            <th>Duration</th>
                            <th>Max Marks</th>
                            <th>Opens</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($upcomingAssessments as $assessment)
                            <tr>
                                <td>
                                    @if($assessment->assessment_type == 'test')
                                        <span class="badge bg-info">
                                            <i class="bi bi-file-text"></i> Test
                                        </span>
                                    @else
                                        <span class="badge bg-primary">
                                            <i class="bi bi-file-earmark-text"></i> Exam
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $assessment->title }}</strong>
                                    @if($assessment->term)
                                        <br>
                                        <small class="text-muted">
                                            {{ $assessment->term }} | {{ $assessment->academic_year }}
                                        </small>
                                    @endif
                                </td>
                                <td>{{ $assessment->subject->name }}</td>
                                <td>{{ $assessment->duration_minutes }} min</td>
                                <td>{{ $assessment->max_marks }}</td>
                                <td>
                                    @if($assessment->schedule_type == 'single_date' && $assessment->start_date)
                                        <div>
                                            {{ $assessment->start_date->timezone(config('app.timezone'))->format('M d, Y') }}
                                        </div>
                                        <small class="text-muted">
                                            {{ $assessment->start_date->timezone(config('app.timezone'))->format('h:i A') }}
                                        </small>
                                    @elseif($assessment->schedule_type == 'date_range' && $assessment->available_from)
                                        <div>
                                            {{ $assessment->available_from->timezone(config('app.timezone'))->format('M d, Y') }}
                                        </div>
                                        <small class="text-muted">
                                            {{ $assessment->available_from->timezone(config('app.timezone'))->format('h:i A') }}
                                        </small>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $startDate = $assessment->schedule_type == 'single_date' 
                                            ? $assessment->start_date 
                                            : $assessment->available_from;
                                    @endphp
                                    @if($startDate)
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-hourglass-split"></i>
                                            {{ $startDate->diffForHumans() }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

<!-- ============================================== -->
<!-- COMPLETED ASSESSMENTS                          -->
<!-- ============================================== -->
<div class="card">
    <div class="card-header bg-white">
        <h6 class="mb-0">
            <i class="bi bi-check-circle text-success"></i>
            Completed Assessments
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Assessment</th>
                        <th>Subject</th>
                        <th>Attempt</th>
                        <th>Score</th>
                        <th>Percentage</th>
                        <th>Result</th>
                        <th>Completed</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($completedExams as $attempt)
                        @php
                            $percentage = $attempt->exam->total_score > 0 
                                ? ($attempt->score / $attempt->exam->total_score) * 100 
                                : 0;
                            
                            $benchmark = $attempt->exam->benchmark ?? $attempt->exam->passing_score ?? 50;
                            $passed = $percentage >= $benchmark;
                            
                            // Convert to max marks scale
                            $scaledScore = $attempt->exam->total_score > 0 
                                ? round(($attempt->score / $attempt->exam->total_score) * $attempt->exam->max_marks, 1)
                                : 0;
                        @endphp
                        <tr>
                            <td>
                                @if($attempt->exam->assessment_type == 'test')
                                    <span class="badge bg-info">
                                        <i class="bi bi-file-text"></i> Test
                                    </span>
                                @else
                                    <span class="badge bg-primary">
                                        <i class="bi bi-file-earmark-text"></i> Exam
                                    </span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $attempt->exam->title }}</strong>
                                @if($attempt->exam->term)
                                    <br>
                                    <small class="text-muted">{{ $attempt->exam->term }}</small>
                                @endif
                            </td>
                            <td>{{ $attempt->exam->subject->name }}</td>
                            <td>
                                <span class="badge bg-secondary">
                                    #{{ $attempt->attempt_number ?? 1 }}
                                </span>
                            </td>
                            <td>
                                <strong>{{ $attempt->score }}</strong>/{{ $attempt->exam->total_score }}
                                <br>
                                <small class="text-muted">
                                    ({{ $scaledScore }}/{{ $attempt->exam->max_marks }} marks)
                                </small>
                            </td>
                            <td>
                                <span class="fw-bold">{{ number_format($percentage, 1) }}%</span>
                            </td>
                            <td>
                                @if($passed)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle"></i> Passed
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle"></i> Failed
                                    </span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $attempt->completed_at ? $attempt->completed_at->format('M d, Y') : 'N/A' }}</small>
                                @if($attempt->completed_at)
                                    <br>
                                    <small class="text-muted">{{ $attempt->completed_at->format('h:i A') }}</small>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('student.exam.result', $attempt->id) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="bi bi-check-circle fs-1 d-block text-muted"></i>
                                <p class="text-muted mt-2 mb-0">No completed assessments yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($completedExams->hasPages())
        <div class="card-footer bg-white">
            {{ $completedExams->links() }}
        </div>
    @endif
</div>
@endsection



@push('styles')
<style>
.assessment-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid #dee2e6;
}
.assessment-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
}
.assessment-test {
    border-left: 4px solid #0dcaf0 !important;
}
.assessment-exam {
    border-left: 4px solid #0d6efd !important;
}
.assessment-card .card-footer {
    padding: 0.5rem 1rem 1rem;
}
.stat-card {
    background: white;
    padding: 1.25rem;
    border-radius: 0.75rem;
    border-left: 4px solid #0d6efd;
    box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
}
.stat-card .stat-icon {
    font-size: 2rem;
    opacity: 0.7;
}
</style>
@endpush