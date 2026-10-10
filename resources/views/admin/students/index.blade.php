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
                                                class="btn btn-sm btn-outline-danger ms-1 deactivate-btn"
                                                title="Deactivate from assessments"
                                                data-student-id="{{ $student->id }}"
                                                data-student-name="{{ $student->user->name }}"
                                                data-student-admission="{{ $student->admission_number }}">
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
                                       class="btn btn-sm btn-outline-primary" title="Edit Student">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                      <button type="button" 
                                            class="btn btn-sm btn-outline-danger delete-student-btn"
                                            title="Delete Student"
                                            data-student-id="{{ $student->id }}"
                                            data-student-name="{{ $student->user->name }}"
                                            data-student-admission="{{ $student->admission_number }}"
                                            data-student-class="{{ $student->class->name ?? 'N/A' }}"
                                            data-student-email="{{ $student->user->email }}">
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


  <!-- ============================================ -->
<!-- SHARED DEACTIVATE MODAL (outside the table)  -->
<!-- ============================================ -->
<div class="modal fade" id="sharedDeactivateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="sharedDeactivateForm" method="POST" action="">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-shield-x text-danger"></i>
                        Deactivate Student from Assessments
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning mb-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-person-x fs-3 me-2"></i>
                            <div>
                                <strong id="modalStudentName">Student Name</strong><br>
                                <small class="text-muted">
                                    Admission: <span id="modalStudentAdmission">-</span>
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <p class="small text-muted mb-3">
                        This student will not be able to take any tests or exams until reactivated.
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Reason for Deactivation <span class="text-danger">*</span>
                        </label>
                        <textarea name="reason" 
                                  id="deactivateReason" 
                                  class="form-control" 
                                  rows="3" 
                                  placeholder="e.g., Outstanding fees, Disciplinary action, Administrative hold"
                                  required></textarea>
                        <small class="text-muted">
                            This reason will be shown to the student when they try to take an assessment.
                        </small>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold">
                            Auto-Reactivate On (Optional)
                        </label>
                        <input type="datetime-local" name="reactivate_at" class="form-control">
                        <small class="text-muted">
                            Leave blank to require manual reactivation.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-shield-x"></i> Deactivate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- SHARED DELETE MODAL                          -->
<!-- ============================================ -->
<div class="modal fade" id="sharedDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="sharedDeleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        Delete Student
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger mb-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-person-x-fill fs-3 me-2"></i>
                            <div>
                                <strong id="deleteStudentName">Student Name</strong><br>
                                <small>
                                    Admission: <span id="deleteStudentAdmission">-</span> | 
                                    Class: <span id="deleteStudentClass">-</span>
                                </small>
                            </div>
                        </div>
                    </div>

                    <p class="mb-2">
                        <strong>Are you sure you want to delete this student?</strong>
                    </p>
                    
                    <div class="alert alert-warning small mb-3">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>This action cannot be undone.</strong>
                        The following data will be permanently removed:
                        <ul class="mb-0 mt-2 ps-3">
                            <li>Student account and login credentials</li>
                            <li>All exam attempts and results</li>
                            <li>All report cards</li>
                        </ul>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small text-muted">
                            Type <strong class="text-danger">DELETE</strong> to confirm:
                        </label>
                        <input type="text" 
                               id="deleteConfirmInput" 
                               class="form-control" 
                               placeholder="Type DELETE here"
                               autocomplete="off">
                        <div class="form-text text-danger" id="deleteConfirmError" style="display: none;">
                            <i class="bi bi-x-circle"></i> Please type DELETE to confirm.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x"></i> Cancel
                    </button>
                    <button type="submit" 
                            class="btn btn-danger" 
                            id="deleteSubmitBtn"
                            disabled>
                        <i class="bi bi-trash"></i> Delete Permanently
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection


@push('scripts')
<script>
$(document).ready(function() {
    // Handle deactivate button clicks
    $(document).on('click', '.deactivate-btn', function() {
        const studentId = $(this).data('student-id');
        const studentName = $(this).data('student-name');
        const studentAdmission = $(this).data('student-admission');

        // Populate the modal
        $('#modalStudentName').text(studentName);
        $('#modalStudentAdmission').text(studentAdmission);

        // Set the form action dynamically
        const actionUrl = '{{ route("admin.students.deactivate-assessments", ":id") }}'
            .replace(':id', studentId);
        $('#sharedDeactivateForm').attr('action', actionUrl);

        // Reset the form fields
        $('#deactivateReason').val('');
        $('#sharedDeactivateForm input[name="reactivate_at"]').val('');

        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('sharedDeactivateModal'));
        modal.show();
    });
});

// ============ SHARED DELETE MODAL ============
$(document).on('click', '.delete-student-btn', function() {
    const studentId = $(this).data('student-id');
    const studentName = $(this).data('student-name');
    const studentAdmission = $(this).data('student-admission');
    const studentClass = $(this).data('student-class');
    const studentEmail = $(this).data('student-email');

    // Populate modal
    $('#deleteStudentName').text(studentName);
    $('#deleteStudentAdmission').text(studentAdmission);
    $('#deleteStudentClass').text(studentClass);

    // Set form action
    const actionUrl = '{{ route("admin.students.delete", ":id") }}'
        .replace(':id', studentId);
    $('#sharedDeleteForm').attr('action', actionUrl);

    // Reset the confirmation input
    $('#deleteConfirmInput').val('');
    $('#deleteConfirmError').hide();
    $('#deleteSubmitBtn').prop('disabled', true);

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('sharedDeleteModal'));
    modal.show();
});

// Enable delete button only when "DELETE" is typed
$(document).on('input', '#deleteConfirmInput', function() {
    const value = $(this).val().trim();
    
    if (value === 'DELETE') {
        $('#deleteSubmitBtn').prop('disabled', false);
        $('#deleteConfirmError').hide();
        $(this).removeClass('is-invalid');
    } else {
        $('#deleteSubmitBtn').prop('disabled', true);
        if (value.length > 0) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    }
});

// Confirm on submit
$(document).on('submit', '#sharedDeleteForm', function(e) {
    const value = $('#deleteConfirmInput').val().trim();
    
    if (value !== 'DELETE') {
        e.preventDefault();
        $('#deleteConfirmError').show();
        $('#deleteConfirmInput').addClass('is-invalid');
        return false;
    }
    
    // Disable the button to prevent double-submit
    $('#deleteSubmitBtn').prop('disabled', true)
        .html('<span class="spinner-border spinner-border-sm"></span> Deleting...');
});

// Reset modal on close
$(document).on('hidden.bs.modal', '#sharedDeleteModal', function() {
    $('#deleteConfirmInput').val('').removeClass('is-invalid');
    $('#deleteConfirmError').hide();
    $('#deleteSubmitBtn').prop('disabled', true)
        .html('<i class="bi bi-trash"></i> Delete Permanently');
});


</script>
@endpush