<!-- resources/views/teacher/scores/create.blade.php -->
@extends('layouts.app')

@section('title', 'Add Score')

@section('sidebar')
    @include('teacher.partials.sidebar')
@endsection

@section('page-title', 'Add Student Score')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="bi bi-plus-circle text-primary"></i>
                    Add Manual Score
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info small">
                    <i class="bi bi-info-circle"></i>
                    Use this form to add scores for tests, assignments, projects, or other assessments that were not taken via the CBT system.
                </div>
                
                <form action="{{ route('teacher.scores.store') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Subject <span class="text-danger">*</span>
                            </label>
                            <select name="subject_id" id="subjectSelect" 
                                    class="form-select @error('subject_id') is-invalid @enderror" required>
                                <option value="">-- Select Subject --</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" 
                                            data-class="{{ $subject->class_id }}"
                                            {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }} ({{ $subject->class->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Student <span class="text-danger">*</span>
                            </label>
                            <select name="student_id" id="studentSelect" 
                                    class="form-select @error('student_id') is-invalid @enderror" required>
                                <option value="">-- Select Subject First --</option>
                            </select>
                            @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Assessment Type <span class="text-danger">*</span>
                            </label>
                            <select name="assessment_type" class="form-select @error('assessment_type') is-invalid @enderror" required>
                                <option value="test" {{ old('assessment_type') == 'test' ? 'selected' : '' }}>Test</option>
                                <option value="exam" {{ old('assessment_type') == 'exam' ? 'selected' : '' }}>Exam</option>
                                <option value="assignment" {{ old('assessment_type') == 'assignment' ? 'selected' : '' }}>Assignment</option>
                                <option value="project" {{ old('assessment_type') == 'project' ? 'selected' : '' }}>Project</option>
                                <option value="quiz" {{ old('assessment_type') == 'quiz' ? 'selected' : '' }}>Quiz</option>
                            </select>
                            @error('assessment_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Assessment Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="assessment_date" 
                                   class="form-control @error('assessment_date') is-invalid @enderror" 
                                   value="{{ old('assessment_date', date('Y-m-d')) }}" 
                                   max="{{ date('Y-m-d') }}" required>
                            @error('assessment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Score <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="score" id="scoreInput" step="0.01"
                                   class="form-control @error('score') is-invalid @enderror" 
                                   value="{{ old('score') }}" min="0" required>
                            @error('score')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Max Score <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="max_score" id="maxScoreInput" step="0.01"
                                   class="form-control @error('max_score') is-invalid @enderror" 
                                   value="{{ old('max_score', 100) }}" min="1" required>
                            @error('max_score')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    
                    <div id="scorePreview" class="alert alert-light border mb-3" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Percentage:</span>
                            <strong id="previewPercentage">0%</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <span>Grade:</span>
                            <span id="previewGrade" class="badge bg-secondary">--</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Remarks (Optional)</label>
                        <textarea name="remarks" class="form-control" rows="2" 
                                  placeholder="Any notes about this score">{{ old('remarks') }}</textarea>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="bi bi-save"></i> Save Score
                        </button>
                        <a href="{{ route('teacher.scores') }}" class="btn btn-secondary px-5">Cancel</a>
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
    // Load students when subject is selected
    $('#subjectSelect').on('change', function() {
        const subjectId = $(this).val();
        const studentSelect = $('#studentSelect');
        
        if (!subjectId) {
            studentSelect.html('<option value="">-- Select Subject First --</option>');
            return;
        }
        
        studentSelect.html('<option value="">Loading students...</option>').prop('disabled', true);
        const baseUrl = '{{ url('/') }}';
        $.ajax({
            url: `${baseUrl}/teacher/scores/students/${subjectId}`,
            method: 'GET',
            dataType: 'json',
            success: function(students) {
                let options = '<option value="">-- Select Student --</option>';
                students.forEach(function(student) {
                    options += `<option value="${student.id}">${student.name} (${student.admission_number})</option>`;
                });
                studentSelect.html(options).prop('disabled', false);
            },
            error: function() {
                studentSelect.html('<option value="">Error loading students</option>').prop('disabled', false);
            }
        });
    });
    
    // Live percentage preview
    function updatePreview() {
        const score = parseFloat($('#scoreInput').val()) || 0;
        const maxScore = parseFloat($('#maxScoreInput').val()) || 0;
        
        if (maxScore > 0) {
            const percentage = (score / maxScore) * 100;
            $('#previewPercentage').text(percentage.toFixed(1) + '%');
            
            let grade = 'F';
            let gradeClass = 'bg-danger';
            if (percentage >= 80) { grade = 'A'; gradeClass = 'bg-success'; }
            else if (percentage >= 70) { grade = 'B'; gradeClass = 'bg-primary'; }
            else if (percentage >= 60) { grade = 'C'; gradeClass = 'bg-info'; }
            else if (percentage >= 50) { grade = 'D'; gradeClass = 'bg-warning'; }
            else if (percentage >= 40) { grade = 'E'; gradeClass = 'bg-warning'; }
            
            $('#previewGrade').text(grade).attr('class', 'badge ' + gradeClass);
            $('#scorePreview').show();
        } else {
            $('#scorePreview').hide();
        }
    }
    
    $('#scoreInput, #maxScoreInput').on('input', updatePreview);
    
    // Load students on page load if subject is pre-selected
    if ($('#subjectSelect').val()) {
        $('#subjectSelect').trigger('change');
    }
});
</script>
@endpush