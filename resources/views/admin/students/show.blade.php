@extends('layouts.app')
@section('title', 'Student Details')
@section('sidebar')
    @include('admin.partials.sidebar')
@endsection
@section('page-title', 'Student Details')

@section('content')
<div class="row">
    <!-- LEFT: Bio & Info -->
    <div class="col-lg-4 mb-4">
        <div class="card text-center">
            <div class="card-body">
                @if($student->user->has_photo)
                    <img src="{{ $student->user->photo_url }}" class="rounded-circle mb-3" 
                         style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #e3e6f0;">
                @else
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-primary text-white mb-3"
                         style="width: 120px; height: 120px; font-size: 2.5rem;">
                        {{ $student->user->initials }}
                    </div>
                @endif
                
                <h4 class="mb-1">{{ $student->user->name }}</h4>
                <p class="text-muted mb-3">{{ $student->admission_number }}</p>
                
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge bg-{{ $student->status == 'active' ? 'success' : 'danger' }}">
                        {{ ucfirst($student->status) }}
                    </span>
                    @if($student->gender)
                        <span class="badge bg-info">{{ $student->gender }}</span>
                    @endif
                </div>
                
                <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-outline-primary w-100">
                    <i class="bi bi-pencil"></i> Edit Student
                </a>&nbsp;&nbsp;
                <a href="{{ route('admin.students.subjects', $student->id) }}" class="btn btn-info w-100 mb-2">
                    <i class="bi bi-book"></i> Manage Subjects
                </a>
            </div>
            <div class="list-group list-group-flush text-start">
                <div class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Class</span>
                    <strong>{{ $student->class->name ?? 'N/A' }}</strong>
                </div>
                <div class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Email</span>
                    <strong>{{ $student->user->email }}</strong>
                </div>
                <div class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Date of Birth</span>
                    <strong>{{ $student->date_of_birth ? $student->date_of_birth->format('M d, Y') : 'N/A' }}</strong>
                </div>
                <div class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Guardian</span>
                    <strong>{{ $student->guardian_name ?? 'N/A' }}</strong>
                </div>
                <div class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Guardian Phone</span>
                    <strong>{{ $student->guardian_phone ?? 'N/A' }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Performance & History -->
    <div class="col-lg-8">
        <!-- Stats Row -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h6 class="text-white-50 mb-1">Avg. Score</h6>
                        <h3 class="mb-0">{{ number_format($averageScore, 1) }}%</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h6 class="text-white-50 mb-1">Exams Passed</h6>
                        <h3 class="mb-0">{{ $passedExams }} / {{ $totalExams }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h6 class="text-white-50 mb-1">Subjects</h6>
                        <h3 class="mb-0">{{ $subjects->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subjects -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-book"></i> Enrolled Subjects</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Subject</th>
                            <th>Code</th>
                            <th>Teacher</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subjects as $subject)
                            <tr>
                                <td><strong>{{ $subject->name }}</strong></td>
                                <td>{{ $subject->code }}</td>
                                <td>{{ $subject->teacher->name ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted">No subjects assigned yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Exam History -->
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-clock-history"></i> Recent Exam History</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Exam / Assessment</th>
                            <th>Subject</th>
                            <th>Score</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($student->examAttempts->where('status', 'submitted')->take(10) as $attempt)
                            <tr>
                                <td>{{ $attempt->exam->title ?? 'N/A' }}</td>
                                <td>{{ $attempt->exam->subject->name ?? 'N/A' }}</td>
                                <td>
                                    <strong>{{ $attempt->score }}</strong>/{{ $attempt->exam->total_score }}
                                </td>
                                <td>
                                    @php
                                        $passed = $attempt->score >= ($attempt->exam->total_score * ($attempt->exam->benchmark / 100));
                                    @endphp
                                    <span class="badge bg-{{ $passed ? 'success' : 'danger' }}">
                                        {{ $passed ? 'Passed' : 'Failed' }}
                                    </span>
                                </td>
                                <td>{{ $attempt->completed_at ? $attempt->completed_at->format('M d, Y') : 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">No exam history yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection