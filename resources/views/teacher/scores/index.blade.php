<!-- resources/views/teacher/scores/index.blade.php -->
@extends('layouts.app')

@section('title', 'Student Scores')

@section('sidebar')
    @include('teacher.partials.sidebar')
@endsection

@section('page-title', 'Student Scores')

@section('content')
<!-- Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card" style="border-left-color: var(--primary-color);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Total Scores</h6>
                    <h3 class="mb-0">{{ $totalScores }}</h3>
                </div>
                <div class="stat-icon text-primary">
                    <i class="bi bi-clipboard-check"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="border-left-color: var(--info-color);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Manual Entries</h6>
                    <h3 class="mb-0">{{ $manualScores }}</h3>
                </div>
                <div class="stat-icon text-info">
                    <i class="bi bi-pencil-square"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="border-left-color: var(--secondary-color);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">CBT Scores</h6>
                    <h3 class="mb-0">{{ $cbtScores }}</h3>
                </div>
                <div class="stat-icon text-success">
                    <i class="bi bi-laptop"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Actions -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('teacher.scores.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Single Score
        </a>
        <a href="{{ route('teacher.scores.bulk-upload') }}" class="btn btn-success">
            <i class="bi bi-upload"></i> Bulk Upload
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Class</label>
                <select name="class_id" class="form-select">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Subject</label>
                <select name="subject_id" class="form-select">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Assessment Type</label>
                <select name="assessment_type" class="form-select">
                    <option value="">All Types</option>
                    <option value="test" {{ request('assessment_type') == 'test' ? 'selected' : '' }}>Test</option>
                    <option value="exam" {{ request('assessment_type') == 'exam' ? 'selected' : '' }}>Exam</option>
                    <option value="assignment" {{ request('assessment_type') == 'assignment' ? 'selected' : '' }}>Assignment</option>
                    <option value="project" {{ request('assessment_type') == 'project' ? 'selected' : '' }}>Project</option>
                    <option value="quiz" {{ request('assessment_type') == 'quiz' ? 'selected' : '' }}>Quiz</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                @if(request()->hasAny(['class_id', 'subject_id', 'assessment_type']))
                    <a href="{{ route('teacher.scores') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-circle"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Results Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Class</th>
                        <th>Subject</th>
                        <th>Assessment</th>
                        <th>Score</th>
                        <th>Percentage</th>
                        <th>Grade</th>
                        <th>Date</th>
                        <th>Source</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $result)
                        <tr>
                            <td>{{ $loop->iteration + ($results->currentPage() - 1) * $results->perPage() }}</td>
                            <td>
                                <strong>{{ $result->student->user->name ?? 'N/A' }}</strong>
                                <br>
                                <small class="text-muted">{{ $result->student->admission_number ?? '' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ $result->student->class->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>{{ $result->subject->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge 
                                    @if($result->assessment_type == 'test') bg-info
                                    @elseif($result->assessment_type == 'exam') bg-primary
                                    @else bg-secondary
                                    @endif">
                                    {{ ucfirst($result->assessment_type) }}
                                </span>
                            </td>
                            <td>
                                <strong>{{ $result->score }}</strong>/{{ $result->max_score }}
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
                            <td>{{ $result->assessment_date->format('M d, Y') }}</td>
                            <td>
                                @if($result->exam_id)
                                    <span class="badge bg-success">
                                        <i class="bi bi-laptop"></i> CBT
                                    </span>
                                @else
                                    <span class="badge bg-info">
                                        <i class="bi bi-pencil"></i> Manual
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if(!$result->exam_id)
                                    <div class="btn-group">
                                        <a href="{{ route('teacher.scores.edit', $result->id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal{{ $result->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-muted small">CBT (locked)</span>
                                @endif
                                
                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $result->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Delete Score</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Delete this score for <strong>{{ $result->student->user->name ?? 'N/A' }}</strong>?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('teacher.scores.delete', $result->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-4">
                                <i class="bi bi-clipboard-check fs-1 d-block text-muted"></i>
                                <p class="text-muted mt-2">No scores uploaded yet.</p>
                                <a href="{{ route('teacher.scores.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-circle"></i> Add First Score
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $results->links() }}
    </div>
</div>
@endsection