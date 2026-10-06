<!-- resources/views/student/take-exam.blade.php -->
@extends('layouts.app')

@section('title', 'Take Exam')

@section('content')
<style>
    .question-palette .q-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 2px solid #dee2e6;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        color: #333;
    }
    .question-palette .q-btn.attempted {
        background: #28a745;
        border-color: #28a745;
        color: white;
    }
    .question-palette .q-btn.unattempted {
        background: #dc3545;
        border-color: #dc3545;
        color: white;
    }
    .question-palette .q-btn.current {
        border-color: #0d6efd;
        border-width: 3px;
        transform: scale(1.1);
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25);
    }
    .question-palette .q-btn:hover {
        transform: scale(1.05);
    }
    .timer {
        font-size: 2rem;
        font-weight: 600;
        color: #dc3545;
        font-variant-numeric: tabular-nums;
    }
    .timer.warning {
        animation: pulse 1s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    .option-item {
        cursor: pointer;
        transition: all 0.2s;
    }
    .option-item:hover {
        background-color: #f8f9fa !important;
        border-color: #0d6efd !important;
    }
    .option-item.selected {
        background-color: #e7f3ff !important;
        border-color: #0d6efd !important;
        border-width: 2px !important;
    }
    .option-item input[type="radio"] {
        cursor: pointer;
    }
    .option-item label {
        cursor: pointer;
    }
</style>

<div class="container-fluid">
    @if(!isset($currentQuestion) || !$currentQuestion)
        <div class="alert alert-danger">
            <h5><i class="bi bi-exclamation-triangle"></i> No Questions Available</h5>
            <p>This exam does not have any questions. Please contact your teacher.</p>
            <a href="{{ route('student.exams') }}" class="btn btn-primary">Back to Exams</a>
        </div>
    @else
        <div class="row">
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $attempt->exam->title }}</h5>
                            <small class="text-muted">{{ $attempt->exam->subject->name }}</small>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">Time Remaining</small>
                            <div class="timer" id="timer">
                                {{ $timeRemaining > 0 ? gmdate('H:i:s', $timeRemaining) : '00:00:00' }}
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @php
                            $currentIndex = $questions->search(function($q) use ($currentQuestion) {
                                return $q->id === $currentQuestion->id;
                            }) + 1;
                        @endphp
                        
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6>Question {{ $currentIndex }} of {{ $totalQuestions }}</h6>
                                <div>
                                    <span class="badge bg-info me-2">Score: {{ $currentQuestion->score }} point(s)</span>
                                    <span class="badge 
                                        @if($currentQuestion->difficulty == 'easy') bg-success
                                        @elseif($currentQuestion->difficulty == 'medium') bg-warning
                                        @else bg-danger
                                        @endif">
                                        {{ ucfirst($currentQuestion->difficulty) }}
                                    </span>
                                </div>
                            </div>
                            <hr>
                            <div class="question-text">
                                <p class="fs-5">{{ $currentQuestion->question_text }}</p>
                               @if($currentQuestion->has_image)
                                    <div class="my-3 text-center">
                                        <img src="{{ $currentQuestion->image_url }}" 
                                            alt="Question Image" 
                                            class="img-fluid rounded border" 
                                            style="max-height: 400px; cursor: pointer;"
                                            onclick="window.open(this.src, '_blank')">
                                        <br>
                                        <small class="text-muted">
                                            <i class="bi bi-zoom-in"></i> Click image to view full size
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div id="options-container">
                            @foreach($currentQuestion->options as $index => $option)
                                @php
                                    $letter = chr(65 + $index);
                                    $isSelected = isset($answers[$currentQuestion->id]) && $answers[$currentQuestion->id] === $option;
                                @endphp
                                <div class="form-check mb-3 p-3 border rounded option-item {{ $isSelected ? 'selected' : '' }}" 
                                     data-question="{{ $currentQuestion->id }}"
                                     data-option="{{ $option }}">
                                    <input class="form-check-input" type="radio" 
                                           name="answer" 
                                           value="{{ $option }}" 
                                           id="option{{ $loop->index }}"
                                           {{ $isSelected ? 'checked' : '' }}>
                                    <label class="form-check-label w-100" for="option{{ $loop->index }}">
                                        <span class="fw-bold">{{ $letter }}.</span> {{ $option }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                        <div>
                            @if($currentIndex > 1)
                                <a href="{{ route('student.exam.continue', $attempt->id) }}?q={{ $questions[$currentIndex - 2]->id }}" 
                                   class="btn btn-outline-primary">
                                    <i class="bi bi-arrow-left"></i> Previous
                                </a>
                            @endif
                        </div>
                        <div>
                            @if($currentIndex < $totalQuestions)
                                <a href="{{ route('student.exam.continue', $attempt->id) }}?q={{ $questions[$currentIndex]->id }}" 
                                   class="btn btn-primary">
                                    Next <i class="bi bi-arrow-right"></i>
                                </a>
                            @endif
                            <button type="button" class="btn btn-success ms-2" data-bs-toggle="modal" data-bs-target="#submitModal">
                                <i class="bi bi-check-circle"></i> Submit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3">
                <div class="card sticky-top" style="top: 1rem;">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">Question Palette</h6>
                    </div>
                    <div class="card-body">
                       <div class="question-palette d-flex flex-wrap gap-2 mb-3">
                            @php
                                // Build a unique list of questions for the palette
                                $paletteQuestions = $questions->unique('id')->values();
                            @endphp
                            
                            @foreach($paletteQuestions as $index => $question)
                                @php
                                    // Strict comparison to prevent type juggling
                                    $isAttempted = array_key_exists($question->id, $answers);
                                    $isCurrent = (int) $currentQuestion->id === (int) $question->id;
                                @endphp
                                <a href="{{ route('student.exam.continue', $attempt->id) }}?q={{ $question->id }}" 
                                class="q-btn 
                                        {{ $isAttempted ? 'attempted' : 'unattempted' }}
                                        {{ $isCurrent ? 'current' : '' }}"
                                data-question-id="{{ $question->id }}">
                                    {{ $index + 1 }}
                                </a>
                            @endforeach
                        </div>
                        
                        <hr>
                        <div class="d-flex justify-content-between small">
                            <div>
                                <span class="badge bg-success me-1">&nbsp;</span> Answered
                                <span class="ms-2" id="answeredCount">{{ $answeredCount }}</span>
                            </div>
                            <div>
                                <span class="badge bg-danger me-1">&nbsp;</span> Unanswered
                                <span class="ms-2" id="unansweredCount">{{ $totalQuestions - $answeredCount }}</span>
                            </div>
                        </div>
                        
                        <hr>
                        <div class="text-center">
                            <small class="text-muted">Question {{ $currentIndex }} of {{ $totalQuestions }}</small>
                        </div>
                        
                        <button type="button" class="btn btn-danger w-100 mt-3" data-bs-toggle="modal" data-bs-target="#submitModal">
                            <i class="bi bi-check-circle"></i> Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Modal -->
        <div class="modal fade" id="submitModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Submit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to submit this exam?</p>
                        <div class="alert alert-info">
                            <strong id="confirmAnswered">{{ $answeredCount }}</strong> 
                            out of <strong>{{ $totalQuestions }}</strong> questions answered.
                        </div>
                        <p class="text-danger"><i class="bi bi-exclamation-triangle"></i> This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form action="{{ route('student.exam.submit', $attempt->id) }}" method="POST" id="submitForm">
                            @csrf
                            <button type="submit" class="btn btn-success">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Time Warning -->
        <div id="timeWarning" style="display: none;" class="alert alert-danger text-center mt-3 position-fixed bottom-0 start-50 translate-middle-x" style="z-index: 1050;">
            <i class="bi bi-exclamation-triangle-fill"></i> 
            <strong>Time is running out!</strong> Your exam will be submitted automatically when the timer reaches zero.
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    @if(isset($currentQuestion) && $currentQuestion)
    
    // ============ CONFIGURATION ============
    const currentQuestionId = {{ $currentQuestion->id }};
    const totalQuestions = {{ $totalQuestions }};
    let isSaving = false;
    
    // ============ TIMER ============
    let timerInterval;
    let timeRemaining = {{ $timeRemaining }};
    let warningShown = false;
    const timerElement = document.getElementById('timer');
    const timeWarning = document.getElementById('timeWarning');
    
    function updateTimer() {
        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            if (timerElement) {
                timerElement.textContent = '00:00:00';
            }
            alert('Time is up! Your exam will be submitted automatically.');
            formSubmitted = true;
            document.getElementById('submitForm').submit();
            return;
        }
        
        const hours = Math.floor(timeRemaining / 3600);
        const minutes = Math.floor((timeRemaining % 3600) / 60);
        const seconds = timeRemaining % 60;
        
        if (timerElement) {
            timerElement.textContent = 
                `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            
            if (timeRemaining < 60 && !warningShown) {
                warningShown = true;
                timerElement.classList.add('warning');
                if (timeWarning) {
                    timeWarning.style.display = 'block';
                }
            }
        }
        
        timeRemaining--;
    }
    
    timerInterval = setInterval(updateTimer, 1000);
    updateTimer();
    
    // ============ ANSWER SELECTION ============
    $(document).on('change', 'input[name="answer"]', function() {
        const answer = $(this).val();
        
        $('.option-item').removeClass('selected');
        $(this).closest('.option-item').addClass('selected');
        
        saveAnswer(currentQuestionId, answer);
    });
    
    $(document).on('click', '.option-item', function(e) {
        if ($(e.target).is('input[type="radio"]')) return;
        if ($(e.target).is('label') || $(e.target).closest('label').length) return;
        
        $(this).find('input[type="radio"]').trigger('click');
    });
    
    // ============ SAVE ANSWER ============
    function saveAnswer(questionId, answer) {
        if (isSaving) return;
        isSaving = true;
        
        $.ajax({
            url: '{{ route("student.exam.save-answer", $attempt->id) }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                question_id: questionId,
                answer: answer
            },
            success: function() {
                const currentBtn = $('.q-btn.current');
                currentBtn.removeClass('unattempted').addClass('attempted');
                
                const answeredCount = $('.q-btn.attempted').length;
                const unansweredCount = totalQuestions - answeredCount;
                
                $('#answeredCount').text(answeredCount);
                $('#unansweredCount').text(unansweredCount);
                $('#confirmAnswered').text(answeredCount);
            },
            error: function(xhr) {
                console.error('Failed to save answer:', xhr.responseText);
                if (xhr.status === 403) {
                    alert('Your session has expired. Please refresh the page.');
                } else if (xhr.status === 400) {
                    alert('This exam has already been submitted.');
                }
            },
            complete: function() {
                isSaving = false;
            }
        });
    }
    
    // ============ KEYBOARD NAVIGATION ============
    $(document).on('keydown', function(e) {
        if ($(e.target).is('input[type="text"], textarea')) return;
        
        if (e.key === 'ArrowLeft') {
            const prevBtn = $('a.btn-outline-primary').filter(function() {
                return $(this).text().includes('Previous');
            });
            if (prevBtn.length) {
                window.location.href = prevBtn.attr('href');
            }
        } else if (e.key === 'ArrowRight') {
            const nextBtn = $('a.btn-primary').filter(function() {
                return $(this).text().includes('Next');
            });
            if (nextBtn.length) {
                window.location.href = nextBtn.attr('href');
            }
        } else if (['1', '2', '3', '4', '5', '6'].includes(e.key)) {
            const index = parseInt(e.key) - 1;
            const options = $('input[name="answer"]');
            if (options.length > index) {
                options.eq(index).prop('checked', true).trigger('change');
            }
        }
    });
    
    // ============ PREVENT ACCIDENTAL SITE LEAVE ============
    // Only warn when actually leaving the exam, NOT for in-exam navigation
    let formSubmitted = false;
    let allowNavigation = false;
    
    // Mark navigation as "allowed" when clicking in-exam links
    $(document).on('click', '.q-btn, a.btn-primary, a.btn-outline-primary, a[href*="/student/exam/"]', function() {
        allowNavigation = true;
        setTimeout(function() {
            allowNavigation = false;
        }, 3000);
    });
    
    // Mark as submitted when form is submitted
    $('#submitForm').on('submit', function() {
        formSubmitted = true;
    });
    
    // Only warn for actual site-leaving actions
    window.addEventListener('beforeunload', function(e) {
        if (formSubmitted || allowNavigation) {
            return;
        }
        
        e.preventDefault();
        e.returnValue = '';
        return '';
    });
    
    @endif
});
</script>
@endpush