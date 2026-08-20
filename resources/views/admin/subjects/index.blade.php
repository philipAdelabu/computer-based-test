<!-- resources/views/admin/subjects/index.blade.php -->
@extends('layouts.app')

@section('title', 'Manage Subjects')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('page-title', 'Subjects')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Subject
        </a>
        <a href="{{ route('admin.subjects.assign') }}" class="btn btn-success">
            <i class="bi bi-person-check"></i> Assign Subject
        </a>
        <a href="{{ route('admin.subjects.bulk-assign') }}" class="btn btn-info">
            <i class="bi bi-people"></i> Bulk Assign
        </a>
    </div>
    <div>
        <a href="{{ route('admin.subjects.stats') }}" class="btn btn-outline-secondary">
            <i class="bi bi-graph-up"></i> Stats
        </a>
        <span class="text-muted ms-2">Total: {{ $subjects->total() }}</span>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Class</th>
                        <th>Teacher</th>
                        <th>Questions</th>
                        <th>Exams</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $subject)
                        <tr>
                            <td>{{ $loop->iteration + ($subjects->currentPage() - 1) * $subjects->perPage() }}</td>
                            <td>
                                <strong>{{ $subject->name }}</strong>
                                @if($subject->description)
                                    <br>
                                    <small class="text-muted">{{ Str::limit($subject->description, 50) }}</small>
                                @endif
                            </td>
                            <td><span class="badge bg-secondary">{{ $subject->code }}</span></td>
                            <td>
                                <span class="badge bg-info">{{ $subject->class->name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                @if($subject->teacher)
                                    <span class="badge bg-primary">{{ $subject->teacher->name }}</span>
                                @else
                                    <span class="badge bg-danger">Not Assigned</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $subject->questions()->count() }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $subject->exams()->count() }}</span>
                            </td>
                            <td>
                                @if($subject->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.subjects.edit', $subject->id) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteModal{{ $subject->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                
                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $subject->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Delete Subject</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete <strong>{{ $subject->name }}</strong>?</p>
                                                @if($subject->questions()->count() > 0 || $subject->exams()->count() > 0)
                                                    <div class="alert alert-danger">
                                                        <i class="bi bi-exclamation-triangle"></i>
                                                        This subject has {{ $subject->questions()->count() }} questions and 
                                                        {{ $subject->exams()->count() }} exams. You must delete them first.
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                @if($subject->questions()->count() == 0 && $subject->exams()->count() == 0)
                                                    <form action="{{ route('admin.subjects.delete', $subject->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="bi bi-book fs-1 d-block text-muted"></i>
                                <p class="text-muted mt-2">No subjects found. Create your first subject!</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $subjects->links() }}
    </div>
</div>
@endsection