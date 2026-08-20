<!-- resources/views/student/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('sidebar')
    @include('student.partials.sidebar')
@endsection

@section('page-title', 'Dashboard')

@section('content')
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
                    <h6 class="text-muted mb-1">Completed Exams</h6>
                    <h3 class="mb-0">{{ $completedExams }}</h3>
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
                    <h6 class="text-muted mb-1">Average Score</h6>
                    <h3 class="mb-0">{{ number_format($averageScore, 1) }}%</h3>
                </div>
                <div class="stat-icon text-warning">
                    <i class="bi bi-graph-up"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color: var(--info-color);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Report Cards</h6>
                    <h3 class="mb-0">{{ auth()->user()->student->reportCards->count() }}</h3>
                </div>
                <div class="stat-icon text-info">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0">Recent Results</h6>
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
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    <a href="{{ route('student.exams') }}" class="btn btn-primary py-3">
                        <i class="bi bi-play-circle me-2"></i> Take Exam
                    </a>
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
@endsection```
