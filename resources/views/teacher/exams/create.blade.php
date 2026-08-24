
@extends('layouts.app')

@section('title', 'Create Exam')

@section('sidebar')
    @include('teacher.partials.sidebar')
@endsection

@section('page-title', 'Create New Exam')

@section('content')
<div class="row">
    <div class="col-lg-10 mx-auto">
        <!-- Add this to your exam create and edit views -->
            <div class="alert alert-info">
                <i class="bi bi-clock"></i>
                <strong>Timezone:</strong> All times are displayed in 
                <strong>{{ config('app.timezone') }}</strong> timezone.
                <br>
                <small>Current server time: {{ Carbon\Carbon::now()->timezone(config('app.timezone'))->format('F d, Y h:i A') }}</small>
            </div>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('teacher.exams.store') }}" method="POST" id="examForm">
                    @csrf
                    
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
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Start Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="start_date" class="form-control @error('start_date') is-invalid @enderror" 
                                   value="{{ old('start_date') }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">End Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="end_date" class="form-control @error('end_date') is-invalid @enderror" 
                                   value="{{ old('end_date') }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Duration (Minutes) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_minutes" class="form-control @error('duration_minutes') is-invalid @enderror" 
                                   value="{{ old('duration_minutes', 30) }}" min="5" max="180" required>
                            @error('duration_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Publish Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_published" value="1" 
                                       id="publishSwitch" {{ old('is_published') ? 'checked' : '' }}>
                                <label class="form-check-label" for="publishSwitch">
                                    <span id="publishLabel">Draft (Not visible to students)</span>
                                </label>
                            </div>
                            <small class="text-muted">Publish to make the exam visible to students</small>
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
                            <i class="bi bi-save"></i> Create Exam
                        </button>
                        <a href="{{ route('teacher.exams') }}" class="btn btn-secondary btn-lg px-5">Cancel</a>
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
    let availableQuestions = [];
    
    // Load questions when subject is selected
    $('#subjectSelect').on('change', function() {
        const subjectId = $(this).val();
        if (!subjectId) {
            $('#noQuestionsMessage').html(`
                <i class="bi bi-info-circle"></i> Select a subject above to load available questions
            `);
            $('#noQuestionsMessage').show();
            $('#questionsList').hide();
            $('#questionStats').hide();
            return;
        }
        
        // Show loading
        $('#noQuestionsMessage').html('<div class="spinner-border text-primary" role="status"></div> Loading questions...');
        $('#noQuestionsMessage').show();
        $('#questionsList').hide();
        $('#questionStats').hide();
        
        // Build the URL correctly with the subjectId
       const baseUrl = "{{ url('/') }}";
        const url = `${baseUrl}/teacher/questions/by-subject/${subjectId}`;
        console.log('Fetching questions from:', url); // Debug log
        
        // Fetch questions
        $.ajax({
            url: url,
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(questions) {
                console.log('Questions loaded:', questions); // Debug log
                availableQuestions = questions;
                
                if (!questions || questions.length === 0) {
                    $('#noQuestionsMessage').html(`
                        <i class="bi bi-info-circle"></i> 
                        No questions available for this subject. 
                        <a href="{{ route('admin.questions.create') }}" class="text-decoration-none">Add questions</a>
                    `);
                    $('#noQuestionsMessage').show();
                    $('#questionsList').hide();
                    $('#questionStats').hide();
                    return;
                }
                
                // Display questions
                let html = '<div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">';
                questions.forEach((question, index) => {
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
                
                $('#questionsList').html(html);
                $('#noQuestionsMessage').hide();
                $('#questionsList').show();
                $('#questionStats').show();
                
                // Add event listeners
                $('.question-checkbox').on('change', function() {
                    updateStats();
                });
                
                // Add select/deselect all buttons if they don't exist
                if ($('#selectAllBtn').length === 0) {
                    $('#questionsList').before(`
                        <div class="mb-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllBtn">
                                <i class="bi bi-check-all"></i> Select All
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllBtn">
                                <i class="bi bi-x-circle"></i> Deselect All
                            </button>
                        </div>
                    `);
                    
                    $('#selectAllBtn').on('click', function() {
                        $('.question-checkbox').prop('checked', true);
                        updateStats();
                    });
                    
                    $('#deselectAllBtn').on('click', function() {
                        $('.question-checkbox').prop('checked', false);
                        updateStats();
                    });
                }
                
                updateStats();
            },
            error: function(xhr, status, error) {
                console.error('Error loading questions:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                
                $('#noQuestionsMessage').html(`
                    <i class="bi bi-exclamation-triangle text-danger"></i> 
                    Error loading questions. Please try again.
                    <br><small class="text-muted">${error}</small>
                `);
                $('#noQuestionsMessage').show();
                $('#questionsList').hide();
                $('#questionStats').hide();
            }
        });
    });
    
    // Update statistics
    function updateStats() {
        const selected = $('.question-checkbox:checked');
        const count = selected.length;
        let totalScore = 0;
        
        selected.each(function() {
            const questionId = $(this).val();
            const question = availableQuestions.find(q => q.id == questionId);
            if (question) {
                totalScore += question.score;
            }
        });
        
        $('#selectedCount').text(count);
        $('#totalScore').text(totalScore);
        $('#totalQuestions').text(availableQuestions.length);
    }
    
    // Publish switch label
    $('#publishSwitch').on('change', function() {
        if ($(this).is(':checked')) {
            $('#publishLabel').text('Published (Visible to students)');
        } else {
            $('#publishLabel').text('Draft (Not visible to students)');
        }
    });
    
    // Form validation
    $('#examForm').on('submit', function(e) {
        const selected = $('.question-checkbox:checked').length;
        if (selected === 0) {
            e.preventDefault();
            alert('Please select at least one question for the exam.');
            return false;
        }
        
        // Show loading
        $('#submitBtn').prop('disabled', true);
        $('#submitBtn').html('<span class="spinner-border spinner-border-sm" role="status"></span> Creating...');
    });
});
</script>
@endpush