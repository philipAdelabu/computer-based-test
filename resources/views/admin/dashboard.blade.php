<!-- resources/views/admin/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('page-title', 'Dashboard')

@section('content')
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Students</h6>
                        <h3 class="mb-0">{{ $totalStudents }}</h3>
                    </div>
                    <div class="stat-icon text-primary">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="border-left-color: var(--secondary-color);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Subjects</h6>
                        <h3 class="mb-0">{{ $totalSubjects ?? 0 }}</h3>
                    </div>
                    <div class="stat-icon text-success">
                        <i class="bi bi-book"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="border-left-color: var(--secondary-color);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Teachers</h6>
                        <h3 class="mb-0">{{ $totalTeachers }}</h3>
                    </div>
                    <div class="stat-icon text-success">
                        <i class="bi bi-person-badge"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="border-left-color: var(--warning-color);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Classes</h6>
                        <h3 class="mb-0">{{ $totalClasses }}</h3>
                    </div>
                    <div class="stat-icon text-warning">
                        <i class="bi bi-building"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="border-left-color: var(--info-color);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Exams</h6>
                        <h3 class="mb-0">{{ $totalExams }}</h3>
                    </div>
                    <div class="stat-icon text-info">
                        <i class="bi bi-file-text"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Quick Actions</h5>
                    <div class="row g-3 mt-2">
                        <div class="col-md-3">
                            <a href="{{ route('admin.students.create') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="bi bi-person-plus fs-4 d-block"></i>
                                Add Student
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.teachers.create') }}" class="btn btn-outline-success w-100 py-3">
                                <i class="bi bi-person-badge-plus fs-4 d-block"></i>
                                Add Teacher
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.classes.create') }}" class="btn btn-outline-warning w-100 py-3">
                                <i class="bi bi-building-add fs-4 d-block"></i>
                                Add Class
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.questions.import') }}" class="btn btn-outline-info w-100 py-3">
                                <i class="bi bi-upload fs-4 d-block"></i>
                                Import Questions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection