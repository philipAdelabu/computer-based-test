<!-- resources/views/admin/teachers/index.blade.php -->
@extends('layouts.app')

@section('title', 'Manage Teachers')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('page-title', 'Teachers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary">
        <i class="bi bi-person-badge-plus"></i> Add Teacher
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Subjects</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachers as $teacher)
                        <tr>
                            <td>{{ $loop->iteration + ($teachers->currentPage() - 1) * $teachers->perPage() }}</td>
                            <td>{{ $teacher->name }}</td>
                            <td>{{ $teacher->email }}</td>
                            <td>{{ $teacher->phone ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-info">{{ $teacher->teacherSubjects->count() }}</span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.teachers.edit', $teacher->id) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteModal{{ $teacher->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                
                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $teacher->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Delete Teacher</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete <strong>{{ $teacher->name }}</strong>?</p>
                                                <p class="text-danger"><i class="bi bi-exclamation-triangle"></i> This action cannot be undone.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('admin.teachers.delete', $teacher->id) }}" method="POST">
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
                            <td colspan="6" class="text-center py-4">
                                <i class="bi bi-person-badge fs-1 d-block text-muted"></i>
                                <p class="text-muted mt-2">No teachers found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $teachers->links() }}
    </div>
</div>
@endsection