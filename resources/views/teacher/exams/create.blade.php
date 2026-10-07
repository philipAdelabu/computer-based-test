<!-- resources/views/teacher/exams/create.blade.php -->
@extends('layouts.app')

@section('title', 'Create Exam/Test')

@section('sidebar')
    @include('teacher.partials.sidebar')
@endsection

@section('page-title', 'Create New Assessment')

@section('content')

   <!-- resources/views/teacher/exams/create.blade.php -->
<!-- Add this section after the Title & Subject row -->

<form action="{{ route('teacher.exams.store') }}" method="POST" id="examForm" novalidate>
            @csrf
<div class="row mb-3">
    <div class="col-md-3">
         
        <label class="form-label fw-semibold">Assessment Type <span class="text-danger">*</span></label>
        <select name="assessment_type" id="assessmentType" class="form-select @error('assessment_type') is-invalid @enderror" required>
            <option value="test" {{ old('assessment_type', 'exam') == 'test' ? 'selected' : '' }}>
                Test (Continuous Assessment)
            </option>
            <option value="exam" {{ old('assessment_type', 'exam') == 'exam' ? 'selected' : '' }}>
                Exam (Examination)
            </option>
        </select>
        @error('assessment_type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Max Marks <span class="text-danger">*</span></label>
        <input type="number" name="max_marks" id="maxMarks" class="form-control @error('max_marks') is-invalid @enderror" 
               value="{{ old('max_marks', 30) }}" min="1" max="200" required>
        <small class="text-muted">e.g., 30 for test, 70 for exam</small>
        @error('max_marks')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Benchmark (%) <span class="text-danger">*</span></label>
        <input type="number" name="benchmark" id="benchmark" class="form-control @error('benchmark') is-invalid @enderror" 
               value="{{ old('benchmark', 50) }}" min="0" max="100" required>
        <small class="text-muted">Passing threshold</small>
        @error('benchmark')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Term <span class="text-danger">*</span></label>
        <select name="term" class="form-select @error('term') is-invalid @enderror" required>
            <option value="First Term" {{ old('term') == 'First Term' ? 'selected' : '' }}>First Term</option>
            <option value="Second Term" {{ old('term') == 'Second Term' ? 'selected' : '' }}>Second Term</option>
            <option value="Third Term" {{ old('term') == 'Third Term' ? 'selected' : '' }}>Third Term</option>
        </select>
        @error('term')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-3">
        <label class="form-label fw-semibold">Academic Year <span class="text-danger">*</span></label>
        <input type="number" name="academic_year" class="form-control @error('academic_year') is-invalid @enderror" 
               value="{{ old('academic_year', date('Y')) }}" min="2020" max="2100" required>
        @error('academic_year')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card">
            <div class="card-body">
               
                    
                    <!-- Basic Information -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Exam Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title') }}" placeholder="e.g., Mathematics Mid-Term Exam" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" id="subjectSelect" class="form-select @error('subject_id') is-invalid @enderror" required>
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }} ({{ $subject->class->name }})
                                          {{ $subject->teacher->name ?? 'No Teacher' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                  rows="3" placeholder="Brief description of the exam">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Schedule Type Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Schedule Type <span class="text-danger">*</span></label>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-check p-3 border rounded schedule-option" data-type="no_date">
                                    <input class="form-check-input" type="radio" name="schedule_type" value="no_date" 
                                           id="noDate" {{ old('schedule_type', 'single_date') == 'no_date' ? 'checked' : '' }}>
                                    <label class="form-check-label d-block" for="noDate">
                                        <h6><i class="bi bi-infinity"></i> No Date</h6>
                                        <small class="text-muted">Always available to students</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check p-3 border rounded schedule-option" data-type="single_date">
                                    <input class="form-check-input" type="radio" name="schedule_type" value="single_date" 
                                           id="singleDate" {{ old('schedule_type', 'single_date') == 'single_date' ? 'checked' : '' }}>
                                    <label class="form-check-label d-block" for="singleDate">
                                        <h6><i class="bi bi-calendar-event"></i> Single Date</h6>
                                        <small class="text-muted">Available on a specific date/time</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check p-3 border rounded schedule-option" data-type="date_range">
                                    <input class="form-check-input" type="radio" name="schedule_type" value="date_range" 
                                           id="dateRange" {{ old('schedule_type') == 'date_range' ? 'checked' : '' }}>
                                    <label class="form-check-label d-block" for="dateRange">
                                        <h6><i class="bi bi-calendar-range"></i> Date Range</h6>
                                        <small class="text-muted">Available for a period</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        @error('schedule_type')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Single Date Fields -->
                    <div id="singleDateFields" class="mb-3" style="display: {{ old('schedule_type', 'single_date') == 'single_date' ? 'block' : 'none' }};">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Start Date & Time <span class="text-danger" id="singleDateStartRequired">*</span></label>
                                <input type="datetime-local" name="start_date" class="form-control @error('start_date') is-invalid @enderror" 
                                       value="{{ old('start_date') }}" 
                                       {{ old('schedule_type', 'single_date') == 'single_date' ? 'required' : '' }}>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">End Date & Time</label>
                                <input type="datetime-local" name="end_date" class="form-control @error('end_date') is-invalid @enderror" 
                                       value="{{ old('end_date') }}">
                                <small class="text-muted">Leave empty for 24-hour availability</small>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Date Range Fields -->
                    <div id="dateRangeFields" class="mb-3" style="display: {{ old('schedule_type') == 'date_range' ? 'block' : 'none' }};">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Available From <span class="text-danger" id="dateRangeFromRequired">*</span></label>
                                <input type="datetime-local" name="available_from" class="form-control @error('available_from') is-invalid @enderror" 
                                       value="{{ old('available_from') }}"
                                       {{ old('schedule_type') == 'date_range' ? 'required' : '' }}>
                                @error('available_from')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Available To <span class="text-danger" id="dateRangeToRequired">*</span></label>
                                <input type="datetime-local" name="available_to" class="form-control @error('available_to') is-invalid @enderror" 
                                       value="{{ old('available_to') }}"
                                       {{ old('schedule_type') == 'date_range' ? 'required' : '' }}>
                                @error('available_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Exam Settings -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Duration (Minutes) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_minutes" class="form-control @error('duration_minutes') is-invalid @enderror" 
                                   value="{{ old('duration_minutes', 30) }}" min="5" max="180" required>
                            @error('duration_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Max Attempts</label>
                            <select name="max_attempts" class="form-select @error('max_attempts') is-invalid @enderror">
                                <option value="1" {{ old('max_attempts', 1) == 1 ? 'selected' : '' }}>1 (Default)</option>
                                <option value="2" {{ old('max_attempts') == 2 ? 'selected' : '' }}>2</option>
                                <option value="3" {{ old('max_attempts') == 3 ? 'selected' : '' }}>3</option>
                                <option value="5" {{ old('max_attempts') == 5 ? 'selected' : '' }}>5</option>
                                <option value="0" {{ old('max_attempts') == 0 ? 'selected' : '' }}>Unlimited</option>
                            </select>
                            @error('max_attempts')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Passing Score (%)</label>
                            <input type="number" name="passing_score" class="form-control @error('passing_score') is-invalid @enderror" 
                                   value="{{ old('passing_score') }}" placeholder="Optional" min="0" max="100">
                            <small class="text-muted">Leave empty for no passing requirement</small>
                            @error('passing_score')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Instructions for Students</label>
                        <textarea name="instructions" class="form-control @error('instructions') is-invalid @enderror" 
                                  rows="4" placeholder="Enter any special instructions for students taking this exam">{{ old('instructions') }}</textarea>
                        @error('instructions')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Additional Options -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_published" value="1" 
                                       id="publishSwitch" {{ old('is_published') ? 'checked' : '' }}>
                                <label class="form-check-label" for="publishSwitch">
                                    <strong>Publish (make visible) </strong>
                                    <br>
                                    <small class="text-muted">Make visible to students</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="show_answers_after_completion" value="1" 
                                       id="showAnswers" {{ old('show_answers_after_completion') ? 'checked' : '' }}>
                                <label class="form-check-label" for="showAnswers">
                                    <strong>Show Answers After Completion</strong>
                                    <br>
                                    <small class="text-muted">Students can review correct answers</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Question Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Select Questions <span class="text-danger">*</span></label>
                        <div id="questionsContainer">
                            <div class="text-center text-muted py-3" id="noQuestionsMessage">
                                <i class="bi bi-info-circle"></i> Select a subject above to load available questions
                            </div>
                            <div id="questionsList" style="display: none;"></div>
                        </div>
                        <div id="questionStats" class="mt-2" style="display: none;">
                            <div class="alert alert-info">
                                <span id="selectedCount">0</span> questions selected | 
                                Total Score: <span id="totalScore">0</span> | 
                                <span id="totalQuestions">0</span> questions
                            </div>
                        </div>
                        @error('question_ids')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg px-5" id="submitBtn">
                            <i class="bi bi-save"></i> Create
                        </button>
                        <a href="{{ route('teacher.exams') }}" class="btn btn-secondary btn-lg px-5">Cancel</a>
                    </div>
               
            </div>
        </div>
    </div>
</div>

 </form>
@endsection

@push('styles')
<style>
.schedule-option {
    cursor: pointer;
    transition: all 0.3s;
}
.schedule-option:hover {
    border-color: #0d6efd;
    background: #f8f9fa;
}
.schedule-option.active {
    border-color: #0d6efd;
    background: #e7f3ff;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Schedule type toggle
    function toggleScheduleFields() {
        const selected = $('input[name="schedule_type"]:checked').val();
        
        // Hide all date fields
        $('#singleDateFields').hide();
        $('#dateRangeFields').hide();
        
        // Remove required attributes from all date inputs
        $('input[name="start_date"]').removeAttr('required');
        $('input[name="end_date"]').removeAttr('required');
        $('input[name="available_from"]').removeAttr('required');
        $('input[name="available_to"]').removeAttr('required');
        
        // Hide asterisks
        $('#singleDateStartRequired').hide();
        $('#dateRangeFromRequired').hide();
        $('#dateRangeToRequired').hide();
        
        // Show relevant fields and set required
        if (selected === 'single_date') {
            $('#singleDateFields').show();
            $('input[name="start_date"]').attr('required', true);
            $('#singleDateStartRequired').show();
        } else if (selected === 'date_range') {
            $('#dateRangeFields').show();
            $('input[name="available_from"]').attr('required', true);
            $('input[name="available_to"]').attr('required', true);
            $('#dateRangeFromRequired').show();
            $('#dateRangeToRequired').show();
        }
    }
    
    // Initialize
    toggleScheduleFields();
    
    // Handle schedule option click
    $('.schedule-option').on('click', function() {
        const radio = $(this).find('input[type="radio"]');
        radio.prop('checked', true);
        $('.schedule-option').removeClass('active');
        $(this).addClass('active');
        toggleScheduleFields();
    });
    
    // Check initial active state
    $('input[name="schedule_type"]:checked').closest('.schedule-option').addClass('active');
    
    // Schedule type change
    $('input[name="schedule_type"]').on('change', function() {
        $('.schedule-option').removeClass('active');
        $(this).closest('.schedule-option').addClass('active');
        toggleScheduleFields();
    });
    
    // Load questions when subject is selected
    let availableQuestions = [];
    
    $('#subjectSelect').on('change', function() {
        const subjectId = $(this).val();
        if (!subjectId) {
            $('#noQuestionsMessage').html(`
                <i class="bi bi-info-circle"></i> Select a subject above to load available questions
            `).show();
            $('#questionsList').hide();
            $('#questionStats').hide();
            return;
        }
        
        // Show loading
        $('#noQuestionsMessage').html(`
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading questions...</p>
        `).show();
        $('#questionsList').hide();
        $('#questionStats').hide();
        
        // Make AJAX request
        const baseURL = "{{ url('/') }}";
        $.ajax({
            url: `${baseURL}/teacher/questions/by-subject/${subjectId}`,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                availableQuestions = response;
                
                if (!response || response.length === 0) {
                    $('#noQuestionsMessage').html(`
                        <i class="bi bi-info-circle"></i> 
                        No questions available for this subject.
                        <br>
                        <a href="{{ route('teacher.questions.create') }}" class="text-decoration-none">
                            <i class="bi bi-plus-circle"></i> Add questions
                        </a>
                    `);
                    return;
                }
                
                let html = '<div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">';
                response.forEach((question, index) => {
                    html += `
                        <div class="form-check p-2 border-bottom">
                            <input class="form-check-input question-checkbox" type="checkbox" 
                                   name="question_ids[]" value="${question.id}" id="q_${question.id}">
                            <label class="form-check-label w-100" for="q_${question.id}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="badge bg-secondary me-2">${index + 1}</span>
                                        ${question.question_text}
                                    </div>
                                    <div>
                                        <span class="badge bg-info">${question.score} pts</span>
                                        <span class="badge 
                                            ${question.difficulty === 'easy' ? 'bg-success' : 
                                              question.difficulty === 'medium' ? 'bg-warning' : 'bg-danger'}">
                                            ${question.difficulty}
                                        </span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    `;
                });
                html += '</div>';
                
                // Add select all buttons
                html = `
                    <div class="mb-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllBtn">
                            <i class="bi bi-check-all"></i> Select All
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllBtn">
                            <i class="bi bi-x-circle"></i> Deselect All
                        </button>
                    </div>
                    ${html}
                `;
                
                $('#questionsList').html(html);
                $('#noQuestionsMessage').hide();
                $('#questionsList').show();
                $('#questionStats').show();
                
                // Event handlers
                $(document).on('change', '.question-checkbox', updateStats);
                $(document).on('click', '#selectAllBtn', function() {
                    $('.question-checkbox').prop('checked', true);
                    updateStats();
                });
                $(document).on('click', '#deselectAllBtn', function() {
                    $('.question-checkbox').prop('checked', false);
                    updateStats();
                });
                
                updateStats();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                $('#noQuestionsMessage').html(`
                    <i class="bi bi-exclamation-triangle text-danger"></i> 
                    Error loading questions. Please refresh and try again.
                    <br><small class="text-muted">${error}</small>
                `);
            }
        });
    });
    
    function updateStats() {
        const selected = $('.question-checkbox:checked');
        const count = selected.length;
        let totalScore = 0;
        
        selected.each(function() {
            const qId = $(this).val();
            const question = availableQuestions.find(q => q.id == qId);
            if (question) totalScore += question.score;
        });
        
        $('#selectedCount').text(count);
        $('#totalScore').text(totalScore);
        $('#totalQuestions').text(availableQuestions.length);
    }
    
    // Form validation
    $('#examForm').on('submit', function(e) {
        // Remove novalidate to allow HTML5 validation
        $(this).removeAttr('novalidate');
        
        // Check if questions are selected
        if ($('.question-checkbox:checked').length === 0) {
            e.preventDefault();
            alert('Please select at least one question for the exam.');
            return false;
        }
        
        // Validate schedule fields
        const scheduleType = $('input[name="schedule_type"]:checked').val();
        if (scheduleType === 'single_date') {
            const startDate = $('input[name="start_date"]').val();
            if (!startDate) {
                e.preventDefault();
                alert('Please set a start date for the exam.');
                return false;
            }
        } else if (scheduleType === 'date_range') {
            const fromDate = $('input[name="available_from"]').val();
            const toDate = $('input[name="available_to"]').val();
            if (!fromDate || !toDate) {
                e.preventDefault();
                alert('Please set both from and to dates for the date range.');
                return false;
            }
        }
        
        // Show loading
        $('#submitBtn').prop('disabled', true);
        $('#submitBtn').html('<span class="spinner-border spinner-border-sm" role="status"></span> Creating...');
    });
    
    // Remove novalidate on page load to allow HTML5 validation
    $('#examForm').removeAttr('novalidate');
});
  
 // Add to your existing <script> section

// Auto-set max_marks and benchmark when assessment type changes
$('#assessmentType').on('change', function() {
    const type = $(this).val();
    const maxMarksInput = $('#maxMarks');
    const benchmarkInput = $('#benchmark');
    
    // Only auto-fill if user hasn't manually changed values
    if (type === 'test') {
        if (maxMarksInput.val() == '70' || maxMarksInput.val() == '') {
            maxMarksInput.val(30);
        }
        if (benchmarkInput.val() == '' || benchmarkInput.val() == '50') {
            benchmarkInput.val(40);
        }
    } else if (type === 'exam') {
        if (maxMarksInput.val() == '30' || maxMarksInput.val() == '') {
            maxMarksInput.val(70);
        }
        if (benchmarkInput.val() == '' || benchmarkInput.val() == '40') {
            benchmarkInput.val(50);
        }
    }
});

// Trigger on page load
$('#assessmentType').trigger('change');

</script>
@endpush