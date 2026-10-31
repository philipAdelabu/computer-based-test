<!-- resources/views/teacher/scores/bulk-upload.blade.php -->
@extends('layouts.app')

@section('title', 'Bulk Upload Scores')

@section('sidebar')
    @include('teacher.partials.sidebar')
@endsection

@section('page-title', 'Bulk Upload Scores')

@section('content')
<div class="row">
    <div class="col-lg-11 mx-auto">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="bi bi-upload text-success"></i>
                    Bulk Upload Scores
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info small">
                    <i class="bi bi-info-circle"></i>
                    Upload scores for an entire class at once. Existing scores for the same date will be updated.
                </div>
                
                <form action="{{ route('teacher.scores.bulk-upload.store') }}" method="POST" id="bulkForm">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" id="subjectSelect" class="form-select" required>
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
                                <option value="quiz">Quiz</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Assessment Date <span class="text-danger">*</span></label>
                            <input type="date" name="assessment_date" class="form-control" 
                                   value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Max Score <span class="text-danger">*</span></label>
                            <input type="number" name="max_score" id="maxScore" 
                                   class="form-control" value="100" min="1" required>
                        </div>
                        <div class="col-md-8 d-flex align-items-end">
                            <div class="text-muted small">
                                <i class="bi bi-info-circle"></i>
                                All students in this subject's class will be listed below.
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6 class="mb-3">Student Scores</h6>
                    <div id="studentsContainer">
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-people fs-1 d-block"></i>
                            <p class="mb-0 mt-2">Select a subject to load students</p>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success btn-lg px-5" id="submitBtn" disabled>
                            <i class="bi bi-upload"></i> Save All Scores
                        </button>
                        <a href="{{ route('teacher.scores') }}" class="btn btn-secondary btn-lg px-5">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#subjectSelect').on('change', function() {
        const subjectId = $(this).val();
        const container = $('#studentsContainer');
        const submitBtn = $('#submitBtn');
        
        if (!subjectId) {
            container.html('<div class="text-center text-muted py-5"><i class="bi bi-people fs-1 d-block"></i><p class="mb-0 mt-2">Select a subject to load students</p></div>');
            submitBtn.prop('disabled', true);
            return;
        }
        
        container.html('<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2 mb-0">Loading students...</p></div>');
        const baseUrl = '{{ url('/') }}';
        $.ajax({
            url: `${baseUrl}/teacher/scores/students/${subjectId}`,
            method: 'GET',
            dataType: 'json',
            success: function(students) {
                if (students.length === 0) {
                    container.html('<div class="alert alert-warning mb-0"><i class="bi bi-exclamation-triangle"></i> No active students found in this class.</div>');
                    submitBtn.prop('disabled', true);
                    return;
                }
                
                let html = '<div class="table-responsive"><table class="table table-bordered">';
                html += '<thead class="table-light"><tr>';
                html += '<th style="width: 50px;">#</th>';
                html += '<th>Student</th>';
                html += '<th style="width: 130px;">Admission #</th>';
                html += '<th style="width: 180px;">Score <small class="text-muted">(Max: <span id="maxScoreDisplay">100</span>)</small></th>';
                html += '</tr></thead><tbody>';
                
                students.forEach(function(student, index) {
                    html += '<tr>';
                    html += `<td class="text-center">${index + 1}</td>`;
                    html += `<td><strong>${student.name}</strong></td>`;
                    html += `<td class="text-muted">${student.admission_number}</td>`;
                    html += `<td><input type="number" name="scores[${student.id}]" class="form-control form-control-sm score-input" step="0.01" min="0" placeholder="--"></td>`;
                    html += '</tr>';
                });
                
                html += '</tbody></table></div>';
                
                // Add summary footer
                html += `<div class="d-flex justify-content-between align-items-center mt-3 p-2 bg-light rounded">
                    <span class="text-muted small">
                        <i class="bi bi-info-circle"></i> Leave score blank to skip that student
                    </span>
                    <div>
                        <strong>Filled:</strong> <span id="filledCount">0</span> / ${students.length}
                    </div>
                </div>`;
                
                container.html(html);
                submitBtn.prop('disabled', false);
                
                // Update max score display
                updateMaxDisplay();
                
                // Track filled count
                $('.score-input').on('input', function() {
                    const filled = $('.score-input').filter(function() {
                        return $(this).val() !== '';
                    }).length;
                    $('#filledCount').text(filled);
                });
            },
            error: function() {
                container.html('<div class="alert alert-danger mb-0"><i class="bi bi-exclamation-triangle"></i> Failed to load students. Please try again.</div>');
                submitBtn.prop('disabled', true);
            }
        });
    });
    
    // Update max score display in the table
    function updateMaxDisplay() {
        $('#maxScoreDisplay').text($('#maxScore').val());
    }
    
    $('#maxScore').on('input', updateMaxDisplay);
    
    // Validation before submit
    $('#bulkForm').on('submit', function(e) {
        const filled = $('.score-input').filter(function() {
            return $(this).val() !== '';
        }).length;
        
        if (filled === 0) {
            e.preventDefault();
            alert('Please enter at least one score.');
            return false;
        }
        
        const maxScore = parseFloat($('#maxScore').val()) || 0;
        let hasError = false;
        
        $('.score-input').each(function() {
            const val = parseFloat($(this).val());
            if (!isNaN(val) && val > maxScore) {
                $(this).addClass('is-invalid');
                hasError = true;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (hasError) {
            e.preventDefault();
            alert('Some scores exceed the maximum score. Please correct them.');
            return false;
        }
        
        $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');
    });
});
</script>
@endpush