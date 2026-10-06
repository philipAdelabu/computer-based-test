
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
                    <h6 class="text-muted mb-1">Available</h6>
                    <h3 class="mb-0">{{ $availableCount }}</h3>
                </div>
                <div class="stat-icon text-primary">
                    <i class="bi bi-file-text"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color: var(--info-color);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Upcoming</h6>
                    <h3 class="mb-0">{{ $upcomingCount }}</h3>
                </div>
                <div class="stat-icon text-info">
                    <i class="bi bi-clock"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color: var(--secondary-color);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Completed</h6>
                    <h3 class="mb-0">{{ $completedCount }}</h3>
                </div>
                <div class="stat-icon text-success">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color: var(--warning-color);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Average</h6>
                    <h3 class="mb-0">{{ number_format($averageScore, 1) }}%</h3>
                </div>
                <div class="stat-icon text-warning">
                    <i class="bi bi-graph-up"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Score Breakdown: Test vs Exam -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card border-info">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="bi bi-file-text text-info"></i> 
                    Continuous Assessment (Tests)
                </h6>
                <span class="badge bg-info">CA</span>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="mb-0 text-info">
                            {{ number_format($totalTestScore, 1) }}
                            <small class="text-muted fs-6">/ {{ $totalTestMax }}</small>
                        </h2>
                        <small class="text-muted">Total test score</small>
                    </div>
                    @php
                        $testPercentage = $totalTestMax > 0 ? ($totalTestScore / $totalTestMax) * 100 : 0;
                    @endphp
                    <div class="text-end">
                        <span class="badge bg-info" style="font-size: 1rem;">
                            {{ number_format($testPercentage, 1) }}%
                        </span>
                    </div>
                </div>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-info" 
                         style="width: {{ $testPercentage }}%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-primary">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="bi bi-file-earmark-text text-primary"></i> 
                    Final Examination
                </h6>
                <span class="badge bg-primary">EXAM</span>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="mb-0 text-primary">
                            {{ number_format($totalExamScore, 1) }}
                            <small class="text-muted fs-6">/ {{ $totalExamMax }}</small>
                        </h2>
                        <small class="text-muted">Total exam score</small>
                    </div>
                    @php
                        $examPercentage = $totalExamMax > 0 ? ($totalExamScore / $totalExamMax) * 100 : 0;
                    @endphp
                    <div class="text-end">
                        <span class="badge bg-primary" style="font-size: 1rem;">
                            {{ number_format($examPercentage, 1) }}%
                        </span>
                    </div>
                </div>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-primary" 
                         style="width: {{ $examPercentage }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Latest Report Card -->
@if($latestReportCard)
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-file-earmark-bar-graph"></i> Latest Report Card</h6>
                    <a href="{{ route('student.report-card.view', $latestReportCard->id) }}" 
                       class="btn btn-sm btn-primary">
                        View Full Report Card
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5>{{ $latestReportCard->class->name }} - {{ $latestReportCard->term }}</h5>
                            <p class="text-muted mb-0">
                                Academic Year: {{ $latestReportCard->academic_year }} | 
                                Position: #{{ $latestReportCard->position }} of {{ $latestReportCard->total_students }}
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <h3 class="mb-0">
                                {{ $latestReportCard->grand_total }}/{{ $latestReportCard->grand_max }}
                                <span class="badge 
                                    @if($latestReportCard->grade == 'A') bg-success
                                    @elseif($latestReportCard->grade == 'B') bg-primary
                                    @elseif($latestReportCard->grade == 'C') bg-info
                                    @elseif($latestReportCard->grade == 'D') bg-warning
                                    @else bg-danger
                                    @endif" 
                                    style="font-size: 1.5rem;">
                                    {{ $latestReportCard->grade }}
                                </span>
                            </h3>
                            <small class="text-muted">{{ $latestReportCard->remarks }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- My Subjects Performance -->
@if(count($subjectBreakdown) > 0)
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-book"></i> My Performance by Subject</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th class="text-center">Test Score</th>
                                    <th class="text-center">Exam Score</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subjectBreakdown as $breakdown)
                                    <tr>
                                        <td>
                                            <strong>{{ $breakdown['subject']->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $breakdown['subject']->code }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">
                                                {{ $breakdown['test_score'] }}/{{ $breakdown['test_max'] }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">
                                                {{ $breakdown['exam_score'] }}/{{ $breakdown['exam_max'] }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <strong>{{ $breakdown['total'] }}/{{ $breakdown['max'] }}</strong>
                                        </td>
                                        <td class="text-center">
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar 
                                                    @if($breakdown['percentage'] >= 70) bg-success
                                                    @elseif($breakdown['percentage'] >= 50) bg-warning
                                                    @else bg-danger
                                                    @endif" 
                                                    style="width: {{ $breakdown['percentage'] }}%">
                                                    {{ number_format($breakdown['percentage'], 1) }}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Available Tests -->
@if($availableTests->count() > 0)
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-info">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <span class="badge bg-info me-2">Tests</span>
                        Available Tests (Continuous Assessment)
                    </h6>
                    <span class="badge bg-info">{{ $availableTests->count() }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($availableTests as $test)
                            <div class="col-md-4">
                                <div class="exam-card" style="border-left: 4px solid #0dcaf0;">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="exam-title">{{ $test->title }}</div>
                                        <span class="badge bg-info">Test</span>
                                    </div>
                                    <div class="exam-meta">
                                        <div><i class="bi bi-book"></i> {{ $test->subject->name }}</div>
                                        <div><i class="bi bi-clock"></i> {{ $test->duration_minutes }} min</div>
                                        <div><i class="bi bi-question-circle"></i> {{ $test->total_questions }} questions</div>
                                        <div>
                                            <i class="bi bi-award"></i> 
                                            Max Marks: <strong>{{ $test->max_marks }}</strong>
                                        </div>
                                        @if($test->schedule_type == 'date_range' && $test->available_to)
                                            <div>
                                                <i class="bi bi-calendar"></i> 
                                                Ends: {{ $test->available_to->timezone(config('app.timezone'))->format('M d, h:i A') }}
                                            </div>
                                        @endif
                                    </div>
                                    @php
                                        $attempt = $test->attempts->first();
                                    @endphp
                                    @if($attempt && $attempt->status === 'in_progress')
                                        <a href="{{ route('student.exam.continue', $attempt->id) }}" 
                                           class="btn btn-warning w-100 mt-3">
                                            <i class="bi bi-play-circle"></i> Continue Test
                                        </a>
                                    @else
                                        <a href="{{ route('student.exam.take', $test->id) }}" 
                                           class="btn btn-info w-100 mt-3">
                                            <i class="bi bi-play-circle"></i> Start Test
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Available Exams -->
@if($availableExams->count() > 0)
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-primary">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <span class="badge bg-primary me-2">Exams</span>
                        Available Exams (Final Examinations)
                    </h6>
                    <span class="badge bg-primary">{{ $availableExams->count() }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($availableExams as $exam)
                            <div class="col-md-4">
                                <div class="exam-card" style="border-left: 4px solid #0d6efd;">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="exam-title">{{ $exam->title }}</div>
                                        <span class="badge bg-primary">Exam</span>
                                    </div>
                                    <div class="exam-meta">
                                        <div><i class="bi bi-book"></i> {{ $exam->subject->name }}</div>
                                        <div><i class="bi bi-clock"></i> {{ $exam->duration_minutes }} min</div>
                                        <div><i class="bi bi-question-circle"></i> {{ $exam->total_questions }} questions</div>
                                        <div>
                                            <i class="bi bi-award"></i> 
                                            Max Marks: <strong>{{ $exam->max_marks }}</strong>
                                        </div>
                                        @if($exam->schedule_type == 'date_range' && $exam->available_to)
                                            <div>
                                                <i class="bi bi-calendar"></i> 
                                                Ends: {{ $exam->available_to->timezone(config('app.timezone'))->format('M d, h:i A') }}
                                            </div>
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
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Upcoming Assessments -->
@if($upcomingAssessments->count() > 0)
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-calendar-event"></i> Upcoming Assessments</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($upcomingAssessments as $assessment)
                            <div class="col-md-4">
                                <div class="exam-card" style="border-left: 4px solid #ffc107;">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="exam-title">{{ $assessment->title }}</div>
                                        <span class="badge 
                                            @if($assessment->assessment_type == 'test') bg-info
                                            @else bg-primary
                                            @endif">
                                            {{ ucfirst($assessment->assessment_type) }}
                                        </span>
                                    </div>
                                    <div class="exam-meta">
                                        <div><i class="bi bi-book"></i> {{ $assessment->subject->name }}</div>
                                        @if($assessment->schedule_type == 'single_date' && $assessment->start_date)
                                            <div>
                                                <i class="bi bi-calendar"></i> 
                                                Starts: {{ $assessment->start_date->timezone(config('app.timezone'))->format('M d, Y h:i A') }}
                                            </div>
                                            <div class="text-muted">
                                                <i class="bi bi-hourglass"></i> 
                                                {{ $assessment->start_date->diffForHumans() }}
                                            </div>
                                        @elseif($assessment->schedule_type == 'date_range' && $assessment->available_from)
                                            <div>
                                                <i class="bi bi-calendar-range"></i> 
                                                Opens: {{ $assessment->available_from->timezone(config('app.timezone'))->format('M d, Y h:i A') }}
                                            </div>
                                            <div class="text-muted">
                                                <i class="bi bi-hourglass"></i> 
                                                {{ $assessment->available_from->diffForHumans() }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Recent Results -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-bar-chart"></i> Recent Results</h6>
                <a href="{{ route('student.results') }}" class="btn btn-sm btn-outline-primary">
                    View All
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Assessment</th>
                                <th>Type</th>
                                <th>Subject</th>
                                <th>Score</th>
                                <th>Percentage</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentResults as $result)
                                <tr>
                                    <td>
                                        {{ $result->exam->title ?? 'N/A' }}
                                    </td>
                                    <td>
                                        @if($result->exam && $result->exam->assessment_type == 'test')
                                            <span class="badge bg-info">Test</span>
                                        @elseif($result->exam)
                                            <span class="badge bg-primary">Exam</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $result->assessment_type }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $result->subject->name ?? 'N/A' }}</td>
                                    <td>
                                        {{ $result->score }}/{{ $result->max_score }}
                                    </td>
                                    <td>{{ number_format($result->percentage, 1) }}%</td>
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
                                    <td colspan="6" class="text-center py-3">
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