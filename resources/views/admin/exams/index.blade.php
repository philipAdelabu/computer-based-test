<!-- resources/views/admin/exams/index.blade.php -->
@extends('layouts.app')

@section('title', 'Manage Tests & Exams')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('page-title', 'Tests & Exams')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.exams.create', ['type' => 'test']) }}" class="btn btn-info">
            <i class="bi bi-plus-circle"></i> Create Test
        </a>
        <a href="{{ route('admin.exams.create', ['type' => 'exam']) }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create Exam
        </a>
    </div>
    <div>
        <span class="text-muted">Total: {{ $exams->total() }}</span>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small">Assessment Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="test" {{ request('type') == 'test' ? 'selected' : '' }}>Tests Only</option>
                    <option value="exam" {{ request('type') == 'exam' ? 'selected' : '' }}>Exams Only</option>
                </select>
            </div>
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
                            {{ $subject->name }} ({{ $subject->class->name }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Status</label>
                <div class="d-flex">
                    <select name="status" class="form-select me-2">
                        <option value="">All</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
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
                        <th>Title</th>
                        <th>Type</th>
                        <th>Subject</th>
                        <th>Max Marks</th>
                        <th>Questions</th>
                        <th>Duration</th>
                        <th>Schedule</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                        <tr>
                            <td>{{ $loop->iteration + ($exams->currentPage() - 1) * $exams->perPage() }}</td>
                            <td>
                                <strong>{{ $exam->title }}</strong>
                                @if($exam->term)
                                    <br>
                                    <small class="text-muted">{{ $exam->term }} | {{ $exam->academic_year }}</small>
                                @endif
                            </td>
                            <td>
                                @if($exam->assessment_type == 'test')
                                    <span class="badge bg-info">Test</span>
                                @else
                                    <span class="badge bg-primary">Exam</span>
                                @endif
                            </td>
                            <td>{{ $exam->subject->name ?? 'N/A' }}</td>
                            <td>{{ $exam->max_marks }}</td>
                            <td>{{ $exam->total_questions }}</td>
                            <td>{{ $exam->duration_minutes }} min</td>
                            <td>
                                @if($exam->schedule_type == 'no_date')
                                    <span class="badge bg-secondary">Always</span>
                                @elseif($exam->schedule_type == 'single_date' && $exam->start_date)
                                    {{ $exam->start_date->timezone(config('app.timezone'))->format('M d, h:i A') }}
                                @elseif($exam->schedule_type == 'date_range' && $exam->available_from)
                                    {{ $exam->available_from->timezone(config('app.timezone'))->format('M d, h:i A') }}
                                @endif
                            </td>
                            <td>
                                @if($exam->is_published)
                                    <span class="badge bg-success">Published</span>
                                @else
                                    <span class="badge bg-secondary">Draft</span>
                                @endif
                            </td>
                            <td>
                                <small>
                                    {{ $exam->creator->name ?? 'N/A' }}
                                    <br>
                                    <span class="badge bg-light text-dark">{{ ucfirst($exam->created_by_role) }}</span>
                                </small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.exams.show', $exam->id) }}" 
                                       class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($exam->status !== 'completed' && $exam->status !== 'active')
                                        <a href="{{ route('admin.exams.edit', $exam->id) }}" 
                                           class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal{{ $exam->id }}"
                                                title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                    @if(!$exam->is_published && $exam->status !== 'completed')
                                        <form action="{{ route('admin.exams.publish', $exam->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Publish">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($exam->is_published && $exam->status !== 'completed')
                                        <form action="{{ route('admin.exams.unpublish', $exam->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Unpublish">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                
                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $exam->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Delete Assessment</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete <strong>{{ $exam->title }}</strong>?</p>
                                                <p class="text-danger">
                                                    <i class="bi bi-exclamation-triangle"></i> 
                                                    This action cannot be undone.
                                                </p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('admin.exams.delete', $exam->id) }}" method="POST">
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
                                <i class="bi bi-file-text fs-1 d-block text-muted"></i>
                                <p class="text-muted mt-2">No assessments found.</p>
                                <a href="{{ route('admin.exams.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-circle"></i> Create Your First Assessment
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $exams->links() }}
    </div>
</div>
@endsection