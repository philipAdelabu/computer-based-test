<!-- resources/views/admin/scores/index.blade.php -->
@extends('layouts.app')

@section('title', 'Student Scores')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('page-title', 'Student Scores')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.scores.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Score
        </a>
        <a href="{{ route('admin.scores.bulk-upload') }}" class="btn btn-success">
            <i class="bi bi-upload"></i> Bulk Upload
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small">Class</label>
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
                <label class="form-label small">Subject</label>
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
                <label class="form-label small">Assessment Type</label>
                <select name="assessment_type" class="form-select">
                    <option value="">All Types</option>
                    <option value="test" {{ request('assessment_type') == 'test' ? 'selected' : '' }}>Test</option>
                    <option value="exam" {{ request('assessment_type') == 'exam' ? 'selected' : '' }}>Exam</option>
                    <option value="assignment" {{ request('assessment_type') == 'assignment' ? 'selected' : '' }}>Assignment</option>
                    <option value="project" {{ request('assessment_type') == 'project' ? 'selected' : '' }}>Project</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $result)
                        <tr>
                            <td>{{ $loop->iteration + ($results->currentPage() - 1) * $results->perPage() }}</td>
                            <td>
                                <strong>{{ $result->student->user->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $result->student->admission_number }}</small>
                            </td>
                            <td>{{ $result->student->class->name ?? 'N/A' }}</td>
                            <td>{{ $result->subject->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge 
                                    @if($result->assessment_type == 'test') bg-info
                                    @elseif($result->assessment_type == 'exam') bg-primary
                                    @else bg-secondary
                                    @endif">
                                    {{ ucfirst($result->assessment_type) }}
                                </span>
                                @if($result->exam)
                                    <br>
                                    <small class="text-muted">{{ $result->exam->title }}</small>
                                @endif
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
                                <div class="btn-group">
                                    <a href="{{ route('admin.scores.edit', $result->id) }}" 
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
                                
                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $result->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Delete Score</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Delete this score for <strong>{{ $result->student->user->name }}</strong>?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('admin.scores.delete', $result->id) }}" method="POST">
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
                            <td colspan="10" class="text-center py-4">
                                <i class="bi bi-clipboard-check fs-1 d-block text-muted"></i>
                                <p class="text-muted mt-2">No scores found.</p>
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