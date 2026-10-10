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
                        <th>Assessment Access</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr class="{{ !$student->is_assessment_active ? 'table-warning' : '' }}">
                            <td>{{ $loop->iteration + ($students->currentPage() - 1) * $students->perPage() }}</td>
                            <td>{{ $student->user->name }}
                                  @if(!$student->is_assessment_active)
                                    <i class="bi bi-shield-exclamation text-danger" 
                                    title="Deactivated from assessments"></i>
                                @endif
                            </td>
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
                                    <!-- Assessment Access Badge / Toggle -->
                                    @if($student->is_assessment_active)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Active
                                        </span>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger ms-1"
                                                title="Deactivate from assessments"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deactivateModal{{ $student->id }}">
                                            <i class="bi bi-shield-x"></i>
                                        </button>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle"></i> Deactivated
                                        </span>
                                        <form action="{{ route('admin.students.reactivate-assessments', $student->id) }}" 
                                            method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-success ms-1"
                                                    title="Reactivate for assessments"
                                                    onclick="return confirm('Reactivate {{ $student->user->name }} for taking assessments?')">
                                                <i class="bi bi-shield-check"></i>
                                            </button>
                                        </form>
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


                                 <!-- Deactivate Modal (per student) -->
                        @if($student->is_assessment_active)
                            <div class="modal fade" id="deactivateModal{{ $student->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.students.deactivate-assessments', $student->id) }}" 
                                            method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    <i class="bi bi-shield-x text-danger"></i>
                                                    Deactivate Student from Assessments
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="alert alert-warning">
                                                    <strong>{{ $student->user->name }}</strong> will not be able 
                                                    to take any tests or exams until reactivated.
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">
                                                        Reason for Deactivation <span class="text-danger">*</span>
                                                    </label>
                                                    <textarea name="reason" class="form-control" rows="3" 
                                                            placeholder="e.g., Outstanding fees, Disciplinary action, Administrative hold"
                                                            required></textarea>
                                                    <small class="text-muted">
                                                        This reason will be shown to the student when they try to take an assessment.
                                                    </small>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">
                                                        Auto-Reactivate On (Optional)
                                                    </label>
                                                    <input type="datetime-local" name="reactivate_at" class="form-control">
                                                    <small class="text-muted">
                                                        Leave blank to keep deactivated until manually reactivated.
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="bi bi-shield-x"></i> Deactivate
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                                @endif
                                
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