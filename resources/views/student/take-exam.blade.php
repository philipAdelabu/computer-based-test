<!-- resources/views/student/take-exam.blade.php -->
@extends('layouts.app')

@section('title', 'Take Exam')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $attempt->exam->title }}</h5>
                    <div class="text-end">
                        <small class="text-muted d-block">Time Remaining</small>
                        <div class="timer" id="timer">{{ gmdate('H:i:s', $timeRemaining) }}</div>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        $currentQuestion = $questions->firstWhere('id', request('q') ?? $questions->first()->id);
                        $totalQuestions = $questions->count();
                        $currentIndex = $questions->search(function($q) use ($currentQuestion) {
                            return $q->id === $currentQuestion->id;
                        }) + 1;
                    @endphp
                    
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6>Question {{ $currentIndex }} of {{ $totalQuestions }}</h6>
                            <span class="badge bg-info">Score: {{ $currentQuestion->score }} point(s)</span>
                        </div>
                        <hr>
                        <div class="question-text">
                            <p class="fs-5">{{ $currentQuestion->question_text }}</p>
                            @if($currentQuestion->image_path)
                                <div class="my-3">
                                    <img src="{{ asset('storage/' . $currentQuestion->image_path) }}" 
                                         alt="Question Image" 
                                         class="img-fluid rounded" 
                                         style="max-height: 300px;">
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <form id="exam-form">
                        @foreach($currentQuestion->options as $index => $option)
                            <div class="form-check mb-3 p-3 border rounded option-item" 
                                 style="cursor: pointer; transition: all 0.2s;">
                                <input class="form-check-input" type="radio" 
                                       name="answer" 
                                       value="{{ $option }}" 
                                       id="option{{ $loop->index }}"
                                       {{ (isset($answers[$currentQuestion->id]) && $answers[$currentQuestion->id] === $option) ? 'checked' : '' }}>
                                <label class="form-check-label w-100" for="option{{ $loop->index }}">
                                    <span class="fw-bold">{{ chr(65 + $loop->index) }}.</span> {{ $option }}
                                </label>
                            </div>
                        @endforeach
                    </form>
                </div>
                <div class="card-footer bg-white d-flex justify-content-between">
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
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#submitModal">
                            <i class="bi bi-check-circle"></i> Submit Exam
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
                    <div class="question-palette">
                        @foreach($questions as $index => $question)
                            <a href="{{ route('student.exam.continue', $attempt->id) }}?q={{ $question->id }}" 
                               class="q-btn 
                                      {{ isset($answers[$question->id]) ? 'attempted' : 'unattempted' }}
                                      {{ $currentQuestion->id === $question->id ? 'current' : '' }}"
                               style="text-decoration: none;">
                                {{ $index + 1 }}
                            </a>
                        @endforeach
                    </div>
                    
                    <hr>
                    <div class="d-flex justify-content-between small">
                        <div>
                            <span class="badge bg-success me-1">&nbsp;</span> Answered
                        </div>
                        <div>
                            <span class="badge bg-danger me-1">&nbsp;</span> Unanswered
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-danger w-100 mt-3" data-bs-toggle="modal" data-bs-target="#submitModal">
                        <i class="bi bi-check-circle"></i> Submit Exam
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Submit Modal -->
<div class="modal fade" id="submitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Submit Exam</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to submit this exam?</p>
                <div class="alert alert-info">
                    <strong>{{ $questions->whereIn('id', array_keys($answers ?? []))->count() }}</strong> 
                    out of <strong>{{ $questions->count() }}</strong> questions answered.
                </div>
                <p class="text-danger"><i class="bi bi-exclamation-triangle"></i> This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('student.exam.submit', $attempt->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success">Submit Exam</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-save answers
    $('input[name="answer"]').on('change', function() {
        const questionId = {{ $currentQuestion->id }};
        const answer = $(this).val();
        
        $.ajax({
            url: '{{ route("student.exam.save-answer", $attempt->id) }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                question_id: questionId,
                answer: answer
            },
            success: function(response) {
                // Update palette
                location.reload();
            }
        });
    });
    
    // Timer countdown
    let timeRemaining = {{ $timeRemaining }};
    const timerElement = document.getElementById('timer');
    
    function updateTimer() {
        if (timeRemaining <= 0) {
            // Auto-submit
            document.getElementById('submitModal').querySelector('form').submit();
            return;
        }
        
        timeRemaining--;
        const hours = Math.floor(timeRemaining / 3600);
        const minutes = Math.floor((timeRemaining % 3600) / 60);
        const seconds = timeRemaining % 60;
        
        timerElement.textContent = 
            `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        
        if (timeRemaining < 60) {
            timerElement.classList.add('warning');
        }
    }
    
    setInterval(updateTimer, 1000);
    
    // Option hover effect
    $('.option-item').on('mouseenter', function() {
        $(this).css('background-color', '#f8f9fa');
    }).on('mouseleave', function() {
        $(this).css('background-color', '');
    });
});
</script>
@endpush