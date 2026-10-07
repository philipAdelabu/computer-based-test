<!-- resources/views/admin/scores/bulk-upload.blade.php -->
@extends('layouts.app')

@section('title', 'Bulk Upload Scores')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('page-title', 'Bulk Upload Scores')

@section('content')
<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.scores.bulk-upload.store') }}" method="POST" id="bulkForm">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Class <span class="text-danger">*</span></label>
                            <select name="class_id" id="classSelect" class="form-select" required>
                                <option value="">-- Select Class --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" class="form-select" required>
                                <option value="">-- Select Subject --</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">
                                        {{ $subject->name }} ({{ $subject->class->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Assessment Type <span class="text-danger">*</span></label>
                            <select name="assessment_type" class="form-select" required>
                                <option value="test">Test</option>
                                <option value="exam">Exam</option>
                                <option value="assignment">Assignment</option>
                                <option value="project">Project</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Max Score <span class="text-danger">*</span></label>
                            <input type="number" name="max_score" class="form-control" value="100" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Assessment Date <span class="text-danger">*</span></label>
                            <input type="date" name="assessment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6>Student Scores</h6>
                    <div id="studentsContainer">
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-people fs-1 d-block"></i>
                            <p>Select a class to load students</p>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success px-5">
                            <i class="bi bi-upload"></i> Save All Scores
                        </button>
                        <a href="{{ route('admin.scores') }}" class="btn btn-secondary px-5">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$('#classSelect').on('change', function() {
    const classId = $(this).val();
    
    if (!classId) {
        $('#studentsContainer').html('<div class="text-center text-muted py-4"><i class="bi bi-people fs-1 d-block"></i><p>Select a class to load students</p></div>');
        return;
    }
    
    $('#studentsContainer').html('<div class="text-center py-4"><div class="spinner-border"></div><p class="mt-2">Loading students...</p></div>');
    const baseUrl = '{{ url('/') }}'; // Get the base URL of the application
    $.ajax({
        url: `${baseUrl}/admin/scores/students/${classId}`,
        method: 'GET',
        success: function(students) {
            if (students.length === 0) {
                $('#studentsContainer').html('<div class="text-center text-muted py-4"><p>No students in this class</p></div>');
                return;
            }
            
            let html = '<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>#</th><th>Student</th><th>Admission #</th><th>Score</th></tr></thead><tbody>';
            
            students.forEach(function(student, index) {
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${student.name}</td>
                        <td>${student.admission_number}</td>
                        <td>
                            <input type="number" 
                                   name="scores[${student.id}]" 
                                   class="form-control" 
                                   min="0" 
                                   placeholder="Enter score">
                        </td>
                    </tr>
                `;
            });
            
            html += '</tbody></table></div>';
            $('#studentsContainer').html(html);
        }
    });
});
</script>
@endpush