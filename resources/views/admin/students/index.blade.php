<!-- resources/views/admin/students/index.blade.php -->
@extends('layouts.app')

@section('title', 'Manage Students')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('page-title', 'Students')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus"></i> Add Student
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
                        <th>Admission #</th>
                        <th>Class</th>
                        <th>Guardian</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>{{ $loop->iteration + ($students->currentPage() - 1) * $students->perPage() }}</td>
                            <td>{{ $student->user->name }}</td>
                            <td>{{ $student->user->email }}</td>
                            <td>{{ $student->admission_number }}</td>
                            <td>{{ $student->class->name ?? 'N/A' }}</td>
                            <td>{{ $student->guardian_name ?? 'N/A' }}</td>
                            <td>
                                @if($student->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($student->status == 'graduated')
                                    <span class="badge bg-info">Graduated</span>
                                @else
                                    <span class="badge bg-danger">Suspended</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.students.edit', $student->id) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteModal{{ $student->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                
                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $student->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Delete Student</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete <strong>{{ $student->user->name }}</strong>?</p>
                                                <p class="text-danger"><i class="bi bi-exclamation-triangle"></i> This action cannot be undone.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('admin.students.delete', $student->id) }}" method="POST">
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
                            <td colspan="8" class="text-center py-4">
                                <i class="bi bi-people fs-1 d-block text-muted"></i>
                                <p class="text-muted mt-2">No students found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $students->links() }}
    </div>
</div>
@endsection