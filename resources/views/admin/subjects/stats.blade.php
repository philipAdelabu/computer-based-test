<!-- resources/views/admin/subjects/stats.blade.php -->
@extends('layouts.app')

@section('title', 'Subject Statistics')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('page-title', 'Subject Statistics')

@section('content')
<div class="row g-4">
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color: var(--primary-color);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Total Subjects</h6>
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
                    <h6 class="text-muted mb-1">Active Subjects</h6>
                    <h3 class="mb-0">{{ $activeSubjects }}</h3>
                </div>
                <div class="stat-icon text-success">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color: var(--danger-color);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Inactive Subjects</h6>
                    <h3 class="mb-0">{{ $inactiveSubjects }}</h3>
                </div>
                <div class="stat-icon text-danger">
                    <i class="bi bi-x-circle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color: var(--info-color);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">With Questions</h6>
                    <h3 class="mb-0">{{ $subjectsWithQuestions }}</h3>
                </div>
                <div class="stat-icon text-info">
                    <i class="bi bi-question-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Subject Distribution</h5>
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <h6>Active vs Inactive</h6>
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar bg-success" style="width: {{ ($activeSubjects / max($totalSubjects, 1)) * 100 }}%">
                                    {{ $activeSubjects }} Active
                                </div>
                                <div class="progress-bar bg-danger" style="width: {{ ($inactiveSubjects / max($totalSubjects, 1)) * 100 }}%">
                                    {{ $inactiveSubjects }} Inactive
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <h6>With Questions</h6>
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar bg-info" style="width: {{ ($subjectsWithQuestions / max($totalSubjects, 1)) * 100 }}%">
                                    {{ $subjectsWithQuestions }} Subjects
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <h6>With Exams</h6>
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar bg-warning" style="width: {{ ($subjectsWithExams / max($totalSubjects, 1)) * 100 }}%">
                                    {{ $subjectsWithExams }} Subjects
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection