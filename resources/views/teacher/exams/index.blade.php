<!-- resources/views/teacher/exams/index.blade.php -->
@extends('layouts.app')

@section('title', 'My Exams')

@section('sidebar')
    @include('teacher.partials.sidebar')
@endsection

@section('page-title', 'My Exams')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('teacher.exams.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Create Exam
    </a>
    <div>
        <span class="text-muted">Total Exams: {{ $exams->total() }}</span>
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
                        <th>Subject</th>
                        <th>Questions</th>
                        <th>Duration</th>
                        <th>Schedule Type</th>
                        <th>Date/Time</th>
                        <th>Status</th>
                        <th>Published</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                        <tr>
                            <td>{{ $loop->iteration + ($exams->currentPage() - 1) * $exams->perPage() }}</td>
                            <td>
                                <strong>{{ $exam->title }}</strong>
                                @if($exam->description)
                                    <br>
                                    <small class="text-muted">{{ Str::limit($exam->description, 50) }}</small>
                                @endif
                            </td>
                            <td>{{ $exam->subject->name }}</td>
                            <td>{{ $exam->total_questions }}</td>
                            <td>{{ $exam->duration_minutes }} min</td>
                            <td>
                                @if($exam->schedule_type == 'no_date')
                                    <span class="badge bg-info">Always Available</span>
                                @elseif($exam->schedule_type == 'single_date')
                                    <span class="badge bg-primary">Single Date</span>
                                @else
                                    <span class="badge bg-warning">Date Range</span>
                                @endif
                            </td>
                            <td>
                                @if($exam->schedule_type == 'no_date')
                                    <span class="text-success">Always Available</span>
                                @elseif($exam->schedule_type == 'single_date')
                                    <div>
                                        <small class="text-muted">Start:</small><br>
                                        <strong>{{ $exam->formatted_start_date }}</strong>
                                    </div>
                                    @if($exam->end_date)
                                        <div>
                                            <small class="text-muted">End:</small><br>
                                            <strong>{{ $exam->formatted_end_date }}</strong>
                                        </div>
                                    @endif
                                @elseif($exam->schedule_type == 'date_range')
                                    <div>
                                        <small class="text-muted">From:</small><br>
                                        <strong>{{ $exam->available_from ? $exam->available_from->timezone(config('app.timezone'))->format('M d, Y h:i A') : 'N/A' }}</strong>
                                    </div>
                                    <div>
                                        <small class="text-muted">To:</small><br>
                                        <strong>{{ $exam->available_to ? $exam->available_to->timezone(config('app.timezone'))->format('M d, Y h:i A') : 'N/A' }}</strong>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="{!! $exam->status_badge !!}">
                                    {{ $exam->status_text }}
                                </span>
                            </td>
                            <td>
                                @if($exam->is_published)
                                    <span class="badge bg-success">Published</span>
                                @else
                                    <span class="badge bg-secondary">Draft</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('teacher.exams.show', $exam->id) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($exam->status !== 'completed' && $exam->status !== 'active')
                                        <a href="{{ route('teacher.exams.edit', $exam->id) }}" 
                                           class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal{{ $exam->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                    @if(!$exam->is_published && $exam->status !== 'completed')
                                        <form action="{{ route('teacher.exams.publish', $exam->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($exam->is_published && $exam->status !== 'completed')
                                        <form action="{{ route('teacher.exams.unpublish', $exam->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">
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
                                                <h5 class="modal-title">Delete Exam</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete <strong>{{ $exam->title }}</strong>?</p>
                                                <p class="text-danger"><i class="bi bi-exclamation-triangle"></i> This action cannot be undone.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('teacher.exams.delete', $exam->id) }}" method="POST">
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
                                <i class="bi bi-file-text fs-1 d-block text-muted"></i>
                                <p class="text-muted mt-2">No exams created yet.</p>
                                <a href="{{ route('teacher.exams.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-circle"></i> Create Your First Exam
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