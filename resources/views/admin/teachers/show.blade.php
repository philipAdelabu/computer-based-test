@extends('layouts.app')
@section('title', 'Teacher Details')
@section('sidebar')
    @include('admin.partials.sidebar')
@endsection
@section('page-title', 'Teacher Details')

@section('content')
<div class="row">
    <!-- LEFT: Bio & Info -->
    <div class="col-lg-4 mb-4">
        <div class="card text-center">
            <div class="card-body">
                @if($teacher->has_photo)
                    <img src="{{ $teacher->photo_url }}" class="rounded-circle mb-3" 
                         style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #e3e6f0;">
                @else
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-success text-white mb-3"
                         style="width: 120px; height: 120px; font-size: 2.5rem;">
                        {{ $teacher->initials }}
                    </div>
                @endif
                
                <h4 class="mb-1">{{ $teacher->name }}</h4>
                <p class="text-muted mb-3">Teacher</p>
                
                <a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="btn btn-outline-primary w-100">
                    <i class="bi bi-pencil"></i> Edit Teacher
                </a>
            </div>
            <div class="list-group list-group-flush text-start">
                <div class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Email</span>
                    <strong>{{ $teacher->email }}</strong>
                </div>
                <div class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Phone</span>
                    <strong>{{ $teacher->phone ?? 'N/A' }}</strong>
                </div>
                <div class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Address</span>
                    <strong>{{ $teacher->address ?? 'N/A' }}</strong>
                </div>
                <div class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Joined</span>
                    <strong>{{ $teacher->created_at->format('M d, Y') }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Classes & Subjects -->
    <div class="col-lg-8">
        <!-- Stats Row -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h6 class="text-white-50 mb-1">Subjects Taught</h6>
                        <h3 class="mb-0">{{ $teacher->teacherSubjects->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h6 class="text-white-50 mb-1">Class Teacher Of</h6>
                        <h3 class="mb-0">{{ $classTeacherOf->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h6 class="text-white-50 mb-1">Active Students</h6>
                        <h3 class="mb-0">{{ $totalStudents }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subjects & Classes -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-book"></i> Assigned Subjects</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Subject</th>
                            <th>Code</th>
                            <th>Class</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teacher->teacherSubjects as $subject)
                            <tr>
                                <td><strong>{{ $subject->name }}</strong></td>
                                <td>{{ $subject->code }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $subject->class->name ?? 'N/A' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted">Not assigned to any subjects.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Class Teacher Of -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-person-badge"></i> Class Teacher Responsibilities</h6>
            </div>
            <div class="card-body">
                @if($classTeacherOf->count() > 0)
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($classTeacherOf as $cls)
                            <span class="badge bg-primary fs-6 p-2">{{ $cls->name }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0">Not assigned as a Class Teacher to any class.</p>
                @endif
            </div>
        </div>

        <!-- Recent Assessments -->
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-file-text"></i> Recent Assessments Created</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Subject</th>
                            <th>Type</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAssessments as $assessment)
                            <tr>
                                <td>{{ $assessment->title }}</td>
                                <td>{{ $assessment->subject->name }}</td>
                                <td>
                                    <span class="badge bg-{{ $assessment->exam_mode === 'theory' ? 'warning text-dark' : 'primary' }}">
                                        {{ ucfirst($assessment->exam_mode) }}
                                    </span>
                                </td>
                                <td>
                                    @if($assessment->is_published)
                                        <span class="badge bg-success">Published</span>
                                    @else
                                        <span class="badge bg-secondary">Draft</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted">No assessments created yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection