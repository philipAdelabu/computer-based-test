@extends('layouts.app')

@section('title', 'Exam Result')

@section('sidebar')
    @include('student.partials.sidebar')
@endsection

@section('page-title', 'Exam Result')

@section('content')
<div class="row">
    <div class="col-lg-10 mx-auto">
        @if($result)
            @php
                $passed = $result->exam->passing_score 
                    ? $result->percentage >= $result->exam->passing_score 
                    : null;
            @endphp
            
            <!-- Summary Card -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="mb-4">
                        <div class="display-1 mb-3">
                            @if($passed === null)
                                <i class="bi bi-check-circle text-primary"></i>
                            @elseif($passed)
                                <i class="bi bi-emoji-smile text-success"></i>
                            @else
                                <i class="bi bi-emoji-frown text-danger"></i>
                            @endif
                        </div>
                        <h4>{{ $attempt->exam->title }}</h4>
                        <p class="text-muted">{{ $attempt->exam->subject->name }}</p>
                    </div>
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted">Score</h6>
                                <h3>{{ $result->score }}/{{ $result->max_score }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted">Percentage</h6>
                                <h3>{{ number_format($result->percentage, 1) }}%</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted">Grade</h6>
                                <h3>
                                    <span class="badge 
                                        @if($result->percentage >= 70) bg-success
                                        @elseif($result->percentage >= 50) bg-warning
                                        @else bg-danger
                                        @endif" 
                                        style="font-size: 1.5rem; padding: 0.5rem 1rem;">
                                        {{ $result->grade }}
                                    </span>
                                </h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted">Status</h6>
                                <h3>
                                    @if($passed === null)
                                        <span class="badge bg-primary" style="font-size: 1.2rem; padding: 0.5rem 1rem;">Completed</span>
                                    @elseif($passed)
                                        <span class="badge bg-success" style="font-size: 1.2rem; padding: 0.5rem 1rem;">Passed</span>
                                    @else
                                        <span class="badge bg-danger" style="font-size: 1.2rem; padding: 0.5rem 1rem;">Failed</span>
                                    @endif
                                </h3>
                            </div>
                        </div>
                    </div>
                    
                    @if($passed !== null)
                        <div class="alert {{ $passed ? 'alert-success' : 'alert-danger' }}">
                            <i class="bi {{ $passed ? 'bi-check-circle' : 'bi-x-circle' }} me-2"></i>
                            @if($passed)
                                Congratulations! You passed the exam.
                            @else
                                You did not meet the passing score of {{ $result->exam->passing_score }}%.
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Statistics Card -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-bar-chart"></i> Performance Breakdown</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="p-3">
                                <h2 class="text-success mb-0">{{ $correctCount }}</h2>
                                <small class="text-muted">Correct</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h2 class="text-danger mb-0">{{ $wrongCount }}</h2>
                                <small class="text-muted">Wrong</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h2 class="text-warning mb-0">{{ $unansweredCount }}</h2>
                                <small class="text-muted">Unanswered</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h2 class="text-primary mb-0">{{ $totalQuestions }}</h2>
                                <small class="text-muted">Total</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="progress mt-3" style="height: 25px;">
                        @if($totalQuestions > 0)
                            <div class="progress-bar bg-success" 
                                 style="width: {{ ($correctCount / $totalQuestions) * 100 }}%">
                                {{ $correctCount }}
                            </div>
                            <div class="progress-bar bg-danger" 
                                 style="width: {{ ($wrongCount / $totalQuestions) * 100 }}%">
                                {{ $wrongCount }}
                            </div>
                            <div class="progress-bar bg-warning" 
                                 style="width: {{ ($unansweredCount / $totalQuestions) * 100 }}%">
                                {{ $unansweredCount }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Question Breakdown -->
            @if($attempt->exam->show_answers_after_completion)
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-list-check"></i> Question Review</h6>
                    </div>
                    <div class="card-body">
                        @foreach($questionBreakdown as $index => $item)
                            <div class="border rounded p-3 mb-3 
                                @if($item['status'] === 'correct') border-success bg-light
                                @elseif($item['status'] === 'wrong') border-danger bg-light
                                @else border-warning bg-light
                                @endif">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0">
                                        <span class="badge bg-secondary me-2">Q{{ $index + 1 }}</span>
                                        {{ $item['question']->question_text }}
                                    </h6>
                                    <div>
                                        @if($item['status'] === 'correct')
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> +{{ $item['score_earned'] }}
                                            </span>
                                        @elseif($item['status'] === 'wrong')
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle"></i> 0
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="bi bi-dash-circle"></i> Not answered
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($item['question']->image_path)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $item['question']->image_path) }}" 
                                             class="img-fluid rounded" 
                                             style="max-height: 200px;">
                                    </div>
                                @endif
                                
                                <div class="row">
                                    @foreach($item['question']->options as $optIndex => $option)
                                        @php
                                            $letter = chr(65 + $optIndex);
                                            $isCorrect = trim($option) === trim($item['correct_answer']);
                                            $isStudentChoice = $item['student_answer'] !== null 
                                                && trim($option) === trim($item['student_answer']);
                                        @endphp
                                        <div class="col-md-6 mb-2">
                                            <div class="p-2 rounded 
                                                @if($isCorrect) bg-success text-white
                                                @elseif($isStudentChoice) bg-danger text-white
                                                @else bg-white border
                                                @endif">
                                                <strong>{{ $letter }}.</strong> {{ $option }}
                                                @if($isCorrect)
                                                    <i class="bi bi-check-circle float-end"></i>
                                                @endif
                                                @if($isStudentChoice && !$isCorrect)
                                                    <i class="bi bi-x-circle float-end"></i>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <!-- Action Buttons -->
            <div class="text-center mb-4">
                <a href="{{ route('student.exams') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Back to Exams
                </a>
                <a href="{{ route('student.results') }}" class="btn btn-outline-primary">
                    <i class="bi bi-bar-chart"></i> View All Results
                </a>
                @if($attempt->exam->max_attempts > 1 && $attempt->exam->is_available)
                    <a href="{{ route('student.exam.take', $attempt->exam->id) }}" 
                       class="btn btn-outline-success">
                        <i class="bi bi-arrow-repeat"></i> Retake Exam
                    </a>
                @endif
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-file-text fs-1 d-block text-muted mb-3"></i>
                    <h5>Result not found</h5>
                    <p class="text-muted">This exam result is not available.</p>
                    <a href="{{ route('student.exams') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-arrow-left"></i> Back to Exams
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection