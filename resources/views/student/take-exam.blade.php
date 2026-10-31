<!-- resources/views/student/take-exam.blade.php -->
@extends('layouts.app')

@section('title', 'Take Exam')

@section('content')
<style>
    .question-palette .q-btn {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        border: 2px solid #dee2e6;
        background: white;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        color: #333;
        margin: 0 4px 8px 0;
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
    .timer {
        font-size: 1.5rem;
        font-weight: 700;
        color: #dc3545;
        font-variant-numeric: tabular-nums;
    }
    .timer.warning { animation: pulse 1s infinite; }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    .option-item {
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        padding: 10px 15px;
        border: 2px solid #e3e6f0;
        border-radius: 8px;
        margin-bottom: 10px;
        background: white;
    }
    .option-item:hover {
        background-color: #f8f9fc;
        border-color: #b8c3d6;
    }
    .option-item.selected {
        background-color: #e7f3ff !important;
        border-color: #0d6efd !important;
    }
    .option-img {
        max-height: 120px;
        max-width: 100%;
        object-fit: contain;
        border-radius: 4px;
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
        @php
            $currentIndex = $questions->search(function($q) use ($currentQuestion) {
                return $q->id === $currentQuestion->id;
            }) + 1;
        @endphp

        <!-- EXAM HEADER -->
        <div class="card mb-3 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center py-2">
                <div>
                    <h5 class="mb-0 fw-bold">{{ $attempt->exam->title }}</h5>
                    <small class="text-muted">{{ $attempt->exam->subject->name }}</small>
                </div>
                <div class="text-end">
                    <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Time Remaining</small>
                    <div class="timer" id="timer">{{ gmdate('H:i:s', $timeRemaining) }}</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <!-- MAIN QUESTION CARD -->
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h6 class="mb-0 fw-bold">Question {{ $currentIndex }} of {{ $totalQuestions }}</h6>
                        <div>
                            <span class="badge bg-info text-dark me-1">{{ $currentQuestion->score }} pt(s)</span>
                            <span class="badge 
                                @if($currentQuestion->difficulty == 'easy') bg-success
                                @elseif($currentQuestion->difficulty == 'medium') bg-warning text-dark
                                @else bg-danger
                                @endif">
                                {{ ucfirst($currentQuestion->difficulty) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="card-body p-4">
                        <!-- Question Text -->
                        <div class="fs-5 mb-4 question-text">
                            {!! \Purifier::clean($currentQuestion->question_text) !!}
                        </div>
                        
                        <!-- Main Question Image (if any) -->
                        @if($currentQuestion->has_image)
                            <div class="my-3 text-center">
                                <img src="{{ $currentQuestion->image_url }}" 
                                     alt="Question Image" 
                                     class="img-fluid rounded border"
                                     style="max-height: 350px; cursor: pointer;"
                                     onclick="window.open(this.src, '_blank')">
                            </div>
                        @endif
                        
                        <!-- ============ OPTIONS LOOP ============ -->
                        <div id="options-container" class="mt-4">
                            @php
                                // Determine how many options we have. 
                                // If images exist, use the image count. Otherwise use the text options count.
                                $optionsCount = $currentQuestion->option_images ? count($currentQuestion->option_images) : count($currentQuestion->options ?? []);
                            @endphp

                            @for ($i = 0; $i < $optionsCount; $i++)
                                @php
                                    $letter = chr(65 + $i);
                                    
                                    // Get text or image for this index
                                    $optionText = $currentQuestion->options[$i] ?? null;
                                    $optionImage = $currentQuestion->option_images[$i] ?? null;
                                    
                                    // The value we save to the DB (either text or image path)
                                    $optionValue = $optionImage ? $optionImage : $optionText;
                                    
                                    // Check if selected
                                    $isSelected = isset($answers[$currentQuestion->id]) && $answers[$currentQuestion->id] == $optionValue;
                                @endphp
                                
                                <div class="option-item {{ $isSelected ? 'selected' : '' }}" 
                                     data-question="{{ $currentQuestion->id }}"
                                     data-option="{{ $optionValue }}">
                                    <input class="form-check-input me-3 mt-0" type="radio" 
                                           name="answer" 
                                           value="{{ $optionValue }}" 
                                           id="option{{ $i }}"
                                           style="width: 20px; height: 20px; flex-shrink: 0;"
                                           {{ $isSelected ? 'checked' : '' }}>
                                    
                                    <label class="form-check-label w-100 d-flex align-items-center" for="option{{ $i }}" style="cursor: pointer;">
                                        <span class="fw-bold me-3 fs-5">{{ $letter }}.</span>
                                        
                                        @if($optionImage)
                                            <img src="{{ asset($optionImage) }}" alt="Option {{ $letter }}" class="option-img">
                                        @elseif($optionText)
                                            <span class="fs-6">{{ $optionText }}</span>
                                        @endif
                                    </label>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <!-- FOOTER NAVIGATION -->
                    <div class="card-footer bg-light d-flex justify-content-between align-items-center py-3">
                        <div>
                            @if($currentIndex > 1)
                                <a href="{{ route('student.exam.continue', $attempt->id) }}?q={{ $questions[$currentIndex - 2]->id }}" 
                                   class="btn btn-outline-primary px-4">
                                    <i class="bi bi-arrow-left"></i> Previous
                                </a>
                            @endif
                        </div>
                        <div>
                            @if($currentIndex < $totalQuestions)
                                <a href="{{ route('student.exam.continue', $attempt->id) }}?q={{ $questions[$currentIndex]->id }}" 
                                   class="btn btn-primary px-4">
                                    Next <i class="bi bi-arrow-right"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- ============ QUESTION PALETTE (NOW AT THE BOTTOM) ============ -->
                <div class="card shadow-sm mb-5">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-grid-3x3-gap-fill"></i> Question Palette</h6>
                    </div>
                    <div class="card-body">
                        <div class="question-palette mb-3 text-center">
                            @foreach($questions as $index => $question)
                                @php
                                    $isAttempted = isset($answers[$question->id]);
                                    $isCurrent = $currentQuestion->id === $question->id;
                                @endphp
                                <a href="{{ route('student.exam.continue', $attempt->id) }}?q={{ $question->id }}" 
                                   class="q-btn 
                                          {{ $isAttempted ? 'attempted' : 'unattempted' }}
                                          {{ $isCurrent ? 'current' : '' }}">
                                    {{ $index + 1 }}
                                </a>
                            @endforeach
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-center gap-4 mb-3 small">
                            <div><span class="badge bg-success">&nbsp;</span> Answered: <strong>{{ $answeredCount }}</strong></div>
                            <div><span class="badge bg-danger">&nbsp;</span> Unanswered: <strong>{{ $totalQuestions - $answeredCount }}</strong></div>
                        </div>

                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-danger px-5 w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#submitModal">
                                <i class="bi bi-check-circle"></i> Submit Exam
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- SUBMIT MODAL -->
        <div class="modal fade" id="submitModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Submit Exam</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="bi bi-exclamation-circle text-warning display-4 mb-3 d-block"></i>
                        <p class="fs-5">Are you sure you want to submit this exam?</p>
                        <div class="alert alert-info">
                            <strong id="confirmAnswered">{{ $answeredCount }}</strong> 
                            out of <strong>{{ $totalQuestions }}</strong> questions answered.
                        </div>
                        <p class="text-danger small mb-0">This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <form action="{{ route('student.exam.submit', $attempt->id) }}" method="POST" id="submitForm">
                            @csrf
                            <button type="submit" class="btn btn-success px-4">Submit Exam</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- TIME WARNING -->
        <div id="timeWarning" style="display: none;" class="alert alert-danger position-fixed bottom-0 start-50 translate-middle-x mb-3 shadow-lg" style="z-index: 1050;">
            <i class="bi bi-exclamation-triangle-fill"></i> <strong>Time is running out!</strong>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    @if(isset($currentQuestion) && $currentQuestion)
    const currentQuestionId = {{ $currentQuestion->id }};
    const totalQuestions = {{ $totalQuestions }};
    let isSaving = false;
    
    // Timer Logic
    let timeRemaining = {{ $timeRemaining }};
    let warningShown = false;
    const timerEl = document.getElementById('timer');
    const timeWarning = document.getElementById('timeWarning');
    
    const timerInterval = setInterval(function() {
        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            if (timerEl) timerEl.textContent = '00:00:00';
            alert('Time is up! Your exam will be submitted.');
            document.getElementById('submitForm').submit();
            return;
        }
        
        const h = Math.floor(timeRemaining / 3600);
        const m = Math.floor((timeRemaining % 3600) / 60);
        const s = timeRemaining % 60;
        
        if (timerEl) {
            timerEl.textContent = 
                `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
            if (timeRemaining < 60 && !warningShown) {
                warningShown = true;
                timerEl.classList.add('warning');
                if (timeWarning) timeWarning.style.display = 'block';
            }
        }
        timeRemaining--;
    }, 1000);
    
    // Answer Selection Logic
    $(document).on('change', 'input[name="answer"]', function() {
        const answer = $(this).val();
        $('.option-item').removeClass('selected');
        $(this).closest('.option-item').addClass('selected');
        saveAnswer(currentQuestionId, answer);
    });
    
    $(document).on('click', '.option-item', function(e) {
        // Prevent if clicking directly on the input or label (browser handles it)
        if ($(e.target).is('input[type="radio"]') || $(e.target).is('label') || $(e.target).closest('label').length) {
            return;
        }
        $(this).find('input[type="radio"]').trigger('click');
    });
    
    function saveAnswer(questionId, answer) {
        if (isSaving) return;
        isSaving = true;
        $.ajax({
            url: '{{ route("student.exam.save-answer", $attempt->id) }}',
            method: 'POST',
            data: { _token: '{{ csrf_token() }}', question_id: questionId, answer: answer },
            complete: function() { isSaving = false; },
            success: function() {
                // Update the palette button live
                $('.q-btn.current').removeClass('unattempted').addClass('attempted');
                const answered = $('.q-btn.attempted').length;
                const unanswered = totalQuestions - answered;
                $('#answeredCount').text(answered);
                $('#unansweredCount').text(unanswered);
                $('#confirmAnswered').text(answered);
            }
        });
    }
    @endif
});
</script>
@endpush