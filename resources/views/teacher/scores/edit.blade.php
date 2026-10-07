<!-- resources/views/teacher/scores/edit.blade.php -->
@extends('layouts.app')

@section('title', 'Edit Score')

@section('sidebar')
    @include('teacher.partials.sidebar')
@endsection

@section('page-title', 'Edit Score')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="bi bi-pencil-square text-primary"></i>
                    Edit Score for {{ $result->student->user->name }}
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('teacher.scores.update', $result->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Read-only student info -->
                    <div class="alert alert-light border">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Student:</strong> {{ $result->student->user->name }}
                            </div>
                            <div class="col-md-6">
                                <strong>Admission:</strong> {{ $result->student->admission_number }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" class="form-select" required>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" 
                                            {{ old('subject_id', $result->subject_id) == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }} ({{ $subject->class->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Assessment Type <span class="text-danger">*</span></label>
                            <select name="assessment_type" class="form-select" required>
                                <option value="test" {{ old('assessment_type', $result->assessment_type) == 'test' ? 'selected' : '' }}>Test</option>
                                <option value="exam" {{ old('assessment_type', $result->assessment_type) == 'exam' ? 'selected' : '' }}>Exam</option>
                                <option value="assignment" {{ old('assessment_type', $result->assessment_type) == 'assignment' ? 'selected' : '' }}>Assignment</option>
                                <option value="project" {{ old('assessment_type', $result->assessment_type) == 'project' ? 'selected' : '' }}>Project</option>
                                <option value="quiz" {{ old('assessment_type', $result->assessment_type) == 'quiz' ? 'selected' : '' }}>Quiz</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Score <span class="text-danger">*</span></label>
                            <input type="number" name="score" id="scoreInput" step="0.01"
                                   class="form-control" value="{{ old('score', $result->score) }}" 
                                   min="0" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Max Score <span class="text-danger">*</span></label>
                            <input type="number" name="max_score" id="maxScoreInput" step="0.01"
                                   class="form-control" value="{{ old('max_score', $result->max_score) }}" 
                                   min="1" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                            <input type="date" name="assessment_date" 
                                   class="form-control" 
                                   value="{{ old('assessment_date', $result->assessment_date->format('Y-m-d')) }}" 
                                   required>
                        </div>
                    </div>
                    
                    <div id="scorePreview" class="alert alert-light border mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Percentage:</span>
                            <strong id="previewPercentage">{{ number_format($result->percentage, 1) }}%</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <span>Grade:</span>
                            <span id="previewGrade" class="badge bg-secondary">{{ $result->grade }}</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="2">{{ old('remarks', $result->remarks) }}</textarea>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="bi bi-save"></i> Update Score
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
        }
    }
    
    $('#scoreInput, #maxScoreInput').on('input', updatePreview);
});
</script>
@endpush