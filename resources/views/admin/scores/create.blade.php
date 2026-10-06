<!-- resources/views/admin/scores/create.blade.php -->
@extends('layouts.app')

@section('title', 'Add Score')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('page-title', 'Add Student Score')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.scores.store') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Class <span class="text-danger">*</span></label>
                            <select name="class_id" id="classSelect" class="form-select" required>
                                <option value="">-- Select Class --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Student <span class="text-danger">*</span></label>
                            <select name="student_id" id="studentSelect" class="form-select" required>
                                <option value="">-- Select Class First --</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
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
                        <div class="col-md-6">
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
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Score <span class="text-danger">*</span></label>
                            <input type="number" name="score" class="form-control" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Max Score <span class="text-danger">*</span></label>
                            <input type="number" name="max_score" class="form-control" value="100" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                            <input type="date" name="assessment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Remarks (Optional)</label>
                        <textarea name="remarks" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="bi bi-save"></i> Save Score
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
    const studentSelect = $('#studentSelect');
    
    if (!classId) {
        studentSelect.html('<option value="">-- Select Class First --</option>');
        return;
    }
    
    studentSelect.html('<option value="">Loading...</option>');
    
    $.ajax({
        url: `/admin/scores/students/${classId}`,
        method: 'GET',
        success: function(students) {
            let options = '<option value="">-- Select Student --</option>';
            students.forEach(function(student) {
                options += `<option value="${student.id}">${student.name} (${student.admission_number})</option>`;
            });
            studentSelect.html(options);
        },
        error: function() {
            studentSelect.html('<option value="">Error loading students</option>');
        }
    });
});
</script>
@endpush