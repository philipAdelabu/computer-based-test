<!-- resources/views/teacher/subjects.blade.php -->
@extends('layouts.app')

@section('title', 'My Subjects')

@section('sidebar')
    @include('teacher.partials.sidebar')
@endsection

@section('page-title', 'My Subjects')

@section('content')
<div class="row g-4">
    @forelse($subjects as $subject)
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title">{{ $subject->name }}</h5>
                            <h6 class="text-muted">Code: {{ $subject->code }}</h6>
                        </div>
                        <span class="badge bg-primary">{{ $subject->class->name }}</span>
                    </div>
                    
                    <hr>
                    <div class="row text-center">
                        <div class="col-4">
                            <h6 class="mb-0">{{ $subject->questions->count() }}</h6>
                            <small class="text-muted">Questions</small>
                        </div>
                        <div class="col-4">
                            <h6 class="mb-0">{{ $subject->exams->count() }}</h6>
                            <small class="text-muted">Exams</small>
                        </div>
                        <div class="col-4">
                            <h6 class="mb-0">{{ $subject->class->students->count() }}</h6>
                            <small class="text-muted">Students</small>
                        </div>
                    </div>
                    
                    @if($subject->description)
                        <hr>
                        <p class="small text-muted mb-0">{{ $subject->description }}</p>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-book fs-1 d-block text-muted"></i>
                <p class="text-muted mt-2">You haven't been assigned to any subjects yet.</p>
            </div>
        </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $subjects->links() }}
</div>


<!-- Add to teacher/subjects.blade.php or a new teacher/students.blade.php -->
 
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Email</th>
                        <th>Admission #</th>
                        <th>Class</th>
                        <th>Guardian</th>
                        <th>Status</th>
                        <th>Assessment Access</th>
                      
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr class="{{ !$student->is_assessment_active ? 'table-warning' : '' }}">
                            <td>{{ $loop->iteration + ($students->currentPage() - 1) * $students->perPage() }}</td>
                             <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($student->user->has_photo)
                                        <img src="{{ $student->user->photo_url }}" 
                                            alt="{{ $student->user->name }}"
                                            class="rounded-circle"
                                            style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-secondary text-white fw-bold"
                                            style="width: 40px; height: 40px; font-size: 0.9rem;">
                                            {{ $student->user->initials }}
                                        </div>
                                    @endif
                                    <div>
                                        <strong>{{ $student->user->name }}</strong>
                                        @if(!$student->is_assessment_active)
                                            <i class="bi bi-shield-exclamation text-danger ms-1"></i>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($student->gender == 'Male')
                                    <span class="badge bg-info">Male</span>
                                @elseif($student->gender == 'Female')
                                    <span class="badge bg-pink" style="background:#e91e63;color:white;">Female</span>
                                @else
                                    <span class="text-muted">—</span>
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
                                        <form action="{{ route('teacher.students.reactivate-assessments', $student->id) }}" 
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
        const actionUrl = '{{ route("teacher.students.deactivate-assessments", ":id") }}'
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





</script>
@endpush