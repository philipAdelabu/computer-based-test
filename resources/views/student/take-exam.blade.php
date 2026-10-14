@extends('layouts.app')

@section('title', 'Take Exam')

@section('content')
<style>
/* ============ EXAM STYLES — MOBILE FIRST ============ */
.exam-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0.5rem;
}

.exam-header {
    background: white;
    border-radius: 0.75rem;
    padding: 0.85rem 1rem;
    box-shadow: 0 0.125rem 0.5rem rgba(0,0,0,0.06);
    margin-bottom: 0.75rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.exam-header h5 { font-size: 1rem; margin: 0; }
.exam-header small { font-size: 0.75rem; }

.exam-timer {
    font-size: 1.3rem;
    font-weight: 700;
    color: #dc3545;
    font-variant-numeric: tabular-nums;
}
.exam-timer.warning {
    animation: pulse 1s infinite;
}

/* Layout: stacked on mobile, side by side on desktop */
.exam-layout {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}

@media (min-width: 992px) {
    .exam-layout {
        grid-template-columns: 1fr 320px;
    }
    .exam-palette-desktop {
        position: sticky;
        top: 80px;
        height: fit-content;
    }
}

/* Question card */
.question-card {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 0.125rem 0.5rem rgba(0,0,0,0.06);
    overflow: hidden;
}

.question-header {
    padding: 0.75rem 1rem;
    background: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.question-body {
    padding: 1rem;
}

.question-text {
    font-size: 1rem;
    line-height: 1.7;
    margin-bottom: 1rem;
}

/* Options — bigger touch targets */
.option-item {
    display: flex;
    align-items: flex-start;
    padding: 0.85rem 1rem;
    border: 2px solid #e3e6f0;
    border-radius: 0.65rem;
    margin-bottom: 0.65rem;
    cursor: pointer;
    transition: all 0.2s;
    -webkit-tap-highlight-color: transparent;
    min-height: 52px;
}
.option-item:hover { border-color: #b8c3d6; background: #f8f9fc; }
.option-item.selected {
    border-color: var(--primary-color);
    background: #eef3ff;
}
.option-item input[type="radio"] {
    width: 22px;
    height: 22px;
    margin-right: 0.75rem;
    margin-top: 2px;
    flex-shrink: 0;
    cursor: pointer;
}
.option-item label {
    font-size: 0.95rem;
    cursor: pointer;
    flex: 1;
    line-height: 1.5;
}

/* Navigation buttons */
.question-nav {
    padding: 0.85rem 1rem;
    background: #f8f9fc;
    border-top: 1px solid #e3e6f0;
    display: flex;
    justify-content: space-between;
    gap: 0.5rem;
}

.question-nav .btn {
    min-width: 100px;
    min-height: 44px;
}

/* Palette — mobile: horizontal scroll; desktop: grid */
.palette-card {
    background: white;
    border-radius: 0.75rem;
    padding: 0.85rem;
    box-shadow: 0 0.125rem 0.5rem rgba(0,0,0,0.06);
}

.palette-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(42px, 1fr));
    gap: 0.5rem;
}

.palette-grid .q-btn {
    width: 100%;
    aspect-ratio: 1;
    min-height: 42px;
    border-radius: 50%;
    border: 2px solid #dee2e6;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.85rem;
    text-decoration: none;
    color: #333;
    transition: all 0.15s;
}
.palette-grid .q-btn.attempted {
    background: #28a745;
    border-color: #28a745;
    color: white;
}
.palette-grid .q-btn.unattempted {
    background: #dc3545;
    border-color: #dc3545;
    color: white;
}
.palette-grid .q-btn.current {
    border-color: #0d6efd;
    border-width: 3px;
    transform: scale(1.08);
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.2);
}

/* Mobile palette at bottom as drawer */
.palette-mobile-toggle {
    position: fixed;
    bottom: 1rem;
    right: 1rem;
    z-index: 1030;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: var(--primary-color);
    color: white;
    border: none;
    font-size: 1.5rem;
    box-shadow: 0 0.5rem 1rem rgba(78, 115, 223, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
}
@media (min-width: 992px) {
    .palette-mobile-toggle { display: none; }
}
</style>

<div class="exam-container">
    @if(!isset($currentQuestion) || !$currentQuestion)
        <div class="alert alert-danger">
            <h5><i class="bi bi-exclamation-triangle"></i> No Questions Available</h5>
            <p>This exam does not have any questions. Please contact your teacher.</p>
            <a href="{{ route('student.exams') }}" class="btn btn-primary">Back to Exams</a>
        </div>
    @else
        @php
            $currentIndex = $questions->search(fn($q) => $q->id === $currentQuestion->id) + 1;
        @endphp

        <!-- HEADER -->
        <div class="exam-header">
            <div class="flex-grow-1">
                <h5>{{ $attempt->exam->title }}</h5>
                <small class="text-muted">{{ $attempt->exam->subject->name }}</small>
            </div>
            <div class="text-end">
                <small class="text-muted d-block" style="font-size: 0.7rem;">TIME LEFT</small>
                <div class="exam-timer" id="timer">{{ gmdate('H:i:s', $timeRemaining) }}</div>
            </div>
        </div>

        <div class="exam-layout">
            <!-- LEFT: Question -->
            <div>
                <div class="question-card">
                    <div class="question-header">
                        <h6 class="mb-0">
                            Question {{ $currentIndex }} of {{ $totalQuestions }}
                        </h6>
                        <div class="d-flex gap-1">
                            <span class="badge bg-info">{{ $currentQuestion->score }} pt</span>
                            <span class="badge @if($currentQuestion->difficulty == 'easy') bg-success @elseif($currentQuestion->difficulty == 'medium') bg-warning @else bg-danger @endif">
                                {{ ucfirst($currentQuestion->difficulty) }}
                            </span>
                        </div>
                    </div>

                    <div class="question-body">
                        <div class="question-text">{!! \Purifier::clean($currentQuestion->question_text) !!}</div>

                        @if($currentQuestion->has_image)
                            <div class="my-3 text-center">
                                <img src="{{ $currentQuestion->image_url }}" 
                                     alt="Question Image" 
                                     class="img-fluid rounded border"
                                     style="max-height: 300px; cursor: pointer;"
                                     onclick="window.open(this.src, '_blank')">
                            </div>
                        @endif

                        <div id="options-container">
                            @foreach($currentQuestion->options as $index => $option)
                                @php
                                    $letter = chr(65 + $index);
                                    $isSelected = isset($answers[$currentQuestion->id]) && $answers[$currentQuestion->id] === $option;
                                @endphp
                                <div class="option-item {{ $isSelected ? 'selected' : '' }}" 
                                     data-question="{{ $currentQuestion->id }}"
                                     data-option="{{ $option }}">
                                    <input class="form-check-input" type="radio" 
                                           name="answer" 
                                           value="{{ $option }}" 
                                           id="option{{ $loop->index }}"
                                           {{ $isSelected ? 'checked' : '' }}>
                                    <label for="option{{ $loop->index }}">
                                        <strong>{{ $letter }}.</strong> {{ $option }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="question-nav">
                        @if($currentIndex > 1)
                            <a href="{{ route('student.exam.continue', $attempt->id) }}?q={{ $questions[$currentIndex - 2]->id }}" 
                               class="btn btn-outline-primary">
                                <i class="bi bi-arrow-left"></i> Prev
                            </a>
                        @else
                            <span></span>
                        @endif

                        <div class="d-flex gap-2">
                            @if($currentIndex < $totalQuestions)
                                <a href="{{ route('student.exam.continue', $attempt->id) }}?q={{ $questions[$currentIndex]->id }}" 
                                   class="btn btn-primary">
                                    Next <i class="bi bi-arrow-right"></i>
                                </a>
                            @endif
                            <button type="button" class="btn btn-success" 
                                    data-bs-toggle="modal" data-bs-target="#submitModal">
                                <i class="bi bi-check-circle"></i> Submit
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Palette (desktop only) -->
            <div class="exam-palette-desktop d-none d-lg-block">
                <div class="palette-card">
                    <h6 class="mb-3">Question Palette</h6>
                    <div class="palette-grid">
                        @foreach($questions as $index => $question)
                            @php
                                $isAttempted = isset($answers[$question->id]);
                                $isCurrent = $currentQuestion->id === $question->id;
                            @endphp
                            <a href="{{ route('student.exam.continue', $attempt->id) }}?q={{ $question->id }}" 
                               class="q-btn {{ $isAttempted ? 'attempted' : 'unattempted' }} {{ $isCurrent ? 'current' : '' }}">
                                {{ $index + 1 }}
                            </a>
                        @endforeach
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between small">
                        <div><span class="badge bg-success">&nbsp;</span> <span id="answeredCount">{{ $answeredCount }}</span> answered</div>
                        <div><span class="badge bg-danger">&nbsp;</span> <span id="unansweredCount">{{ $totalQuestions - $answeredCount }}</span> left</div>
                    </div>

                    <button type="button" class="btn btn-danger w-100 mt-3" 
                            data-bs-toggle="modal" data-bs-target="#submitModal">
                        <i class="bi bi-check-circle"></i> Submit Exam
                    </button>
                </div>
            </div>
        </div>

        <!-- MOBILE: Floating palette toggle -->
        <button type="button" class="palette-mobile-toggle d-lg-none" 
                data-bs-toggle="offcanvas" data-bs-target="#paletteDrawer">
            <i class="bi bi-grid-3x3-gap-fill"></i>
        </button>

        <!-- MOBILE: Palette drawer -->
        <div class="offcanvas offcanvas-bottom d-lg-none" tabindex="-1" id="paletteDrawer" 
             style="height: 60vh;">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title">Question Palette</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body">
                <div class="palette-grid mb-3">
                    @foreach($questions as $index => $question)
                        @php
                            $isAttempted = isset($answers[$question->id]);
                            $isCurrent = $currentQuestion->id === $question->id;
                        @endphp
                        <a href="{{ route('student.exam.continue', $attempt->id) }}?q={{ $question->id }}" 
                           class="q-btn {{ $isAttempted ? 'attempted' : 'unattempted' }} {{ $isCurrent ? 'current' : '' }}">
                            {{ $index + 1 }}
                        </a>
                    @endforeach
                </div>
                <div class="d-flex justify-content-between small mb-3">
                    <div><span class="badge bg-success">&nbsp;</span> Answered: <span id="answeredCountMobile">{{ $answeredCount }}</span></div>
                    <div><span class="badge bg-danger">&nbsp;</span> Unanswered: <span id="unansweredCountMobile">{{ $totalQuestions - $answeredCount }}</span></div>
                </div>
                <button type="button" class="btn btn-danger w-100" 
                        data-bs-toggle="modal" data-bs-target="#submitModal">
                    <i class="bi bi-check-circle"></i> Submit Exam
                </button>
            </div>
        </div>

        <!-- SUBMIT MODAL -->
        <div class="modal fade" id="submitModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Submit Exam?</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <strong>{{ $answeredCount }}</strong> out of 
                            <strong>{{ $totalQuestions }}</strong> questions answered.
                        </div>
                        <p class="text-danger small mb-0">
                            <i class="bi bi-exclamation-triangle"></i> This action cannot be undone.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form action="{{ route('student.exam.submit', $attempt->id) }}" method="POST" id="submitForm">
                            @csrf
                            <button type="submit" class="btn btn-success">Submit Exam</button>
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

@push('styles')
<style>
    .math {
        font-family: 'Times New Roman', serif;
        font-style: italic;
    }
    .question-text {
    line-height: 1.6;
    }
    .question-text img {
        max-width: 100%;
        height: auto;
        border-radius: 6px;
    }
    .question-text table {
        width: 100%;
        border-collapse: collapse;
        margin: 10px 0;
    }
    .question-text table td,
    .question-text table th {
        border: 1px solid #dee2e6;
        padding: 6px 10px;
    }
    .question-text ul,
    .question-text ol {
        padding-left: 25px;
    }
    .question-text code {
        background: #f4f4f4;
        padding: 2px 6px;
        border-radius: 4px;
        font-family: 'Courier New', monospace;
    }
</style>
@endpush

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