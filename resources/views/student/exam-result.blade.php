<!-- resources/views/student/exam-result.blade.php -->
@extends('layouts.app')

@section('title', 'Exam Result')

@section('sidebar')
    @include('student.partials.sidebar')
@endsection

@section('page-title', 'Assessment Result')

@section('content')
@php
    // Determine if the exam is pending teacher grading
    $isPending = ($attempt->exam->show_answers_after_completion === false) || ($result->remarks === 'Pending Theory Grading' && $result->score == 0);
    
    // Check if student passed based on the benchmark
    $passed = $result->exam->passing_score 
        ? $result->percentage >= $result->exam->passing_score 
        : null;

    // Recount for the breakdown (so theory doesn't show as wrong)
    $objCorrectCount = 0;
    $objWrongCount = 0;
    $theoryPendingCount = 0;
    
    foreach($attempt->exam->questions as $q) {
        if ($q->is_theory) {
            $theoryPendingCount++;
        } elseif (isset($attempt->answers[$q->id])) {
            if (trim($attempt->answers[$q->id]) === trim($q->correct_answer)) {
                $objCorrectCount++;
            } else {
                $objWrongCount++;
            }
        }
    }
@endphp

<div class="row">
    <div class="col-lg-10 mx-auto">
        @if($result)
            <!-- ============ SUMMARY CARD ============ -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="mb-4">
                        <div class="display-1 mb-3">
                            @if($isPending)
                                <i class="bi bi-clock-history text-warning"></i>
                            @elseif($passed === null)
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
                                    @if($isPending)
                                        <span class="badge bg-secondary" style="font-size: 1.5rem; padding: 0.5rem 1.5rem;">—</span>
                                    @else
                                        <span class="badge 
                                            @if($result->percentage >= 70) bg-success
                                            @elseif($result->percentage >= 50) bg-warning
                                            @else bg-danger
                                            @endif" 
                                            style="font-size: 1.5rem; padding: 0.5rem 1.5rem;">
                                            {{ $result->grade }}
                                        </span>
                                    @endif
                                </h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted">Status</h6>
                                <h3>
                                    @if($isPending)
                                        <span class="badge bg-warning text-dark" style="font-size: 1.2rem; padding: 0.5rem 1rem;">Pending</span>
                                    @elseif($passed === null)
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
                    
                    @if($isPending)
                        <div class="alert alert-warning">
                            <i class="bi bi-clock-history me-2"></i>
                            This assessment contains theory questions. Your final result will be available once your teacher grades them.
                        </div>
                    @elseif($passed !== null)
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
            
            <!-- ============ STATISTICS / BREAKDOWN CARD ============ -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-bar-chart"></i> Performance Breakdown</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="p-3">
                                <h2 class="text-success mb-0">{{ $objCorrectCount }}</h2>
                                <small class="text-muted">Correct (Objective)</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h2 class="text-danger mb-0">{{ $objWrongCount }}</h2>
                                <small class="text-muted">Wrong (Objective)</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h2 class="text-warning mb-0">{{ $theoryPendingCount }}</h2>
                                <small class="text-muted">Pending Theory</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h2 class="text-primary mb-0">{{ $attempt->exam->questions->count() }}</h2>
                                <small class="text-muted">Total Questions</small>
                            </div>
                        </div>
                    </div>
                    
                    @if($theoryPendingCount == 0)
                        <div class="progress mt-3" style="height: 25px;">
                            <div class="progress-bar bg-success" 
                                 style="width: {{ ($objCorrectCount / $attempt->exam->questions->count()) * 100 }}%">
                                {{ $objCorrectCount }}
                            </div>
                            <div class="progress-bar bg-danger" 
                                 style="width: {{ ($objWrongCount / $attempt->exam->questions->count()) * 100 }}%">
                                {{ $objWrongCount }}
                            </div>
                            <div class="progress-bar bg-warning" 
                                 style="width: {{ (($attempt->exam->questions->count() - $objCorrectCount - $objWrongCount) / $attempt->exam->questions->count()) * 100 }}%">
                                Unanswered
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- ============ QUESTION REVIEW CARD ============ -->
            @if($attempt->exam->show_answers_after_completion)
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-list-check"></i> Question Review</h6>
                    </div>
                    <div class="card-body">
                        @foreach($questionBreakdown as $index => $item)
                            @php
                                $isTheory = $item['question']->is_theory;
                            @endphp
                            
                            <div class="border rounded p-3 mb-3 
                                @if($isTheory) border-warning bg-light
                                @elseif($item['status'] === 'correct') border-success bg-light
                                @elseif($item['status'] === 'wrong') border-danger bg-light
                                @else border-warning bg-light
                                @endif">
                                
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0">
                                        <span class="badge bg-secondary me-2">Q{{ $index + 1 }}</span>
                                        {!! \Purifier::clean($item['question']->question_text) !!}
                                    </h6>
                                    <div>
                                        @if($isTheory)
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-pencil-square"></i> Theory
                                            </span>
                                        @elseif($item['status'] === 'correct')
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
                                
                                @if($item['question']->has_image)
                                    <div class="mb-2">
                                        <img src="{{ $item['question']->image_url }}" 
                                             class="img-fluid rounded" 
                                             style="max-height: 200px;">
                                    </div>
                                @endif
                                
                                <!-- ============ OPTIONS RENDERING ============ -->
                                @if($isTheory)
                                    <div class="mt-3 p-3 bg-white border rounded">
                                        <strong class="text-primary"><i class="bi bi-pencil-square"></i> Student's Answer:</strong>
                                        <div class="mt-2 text-dark" style="white-space: pre-wrap;">{{ $item['student_answer'] ?? 'No answer provided.' }}</div>
                                    </div>
                                @else
                                    <div class="row">
                                        @foreach($item['question']->options as $optIndex => $option)
                                            @php
                                                $letter = chr(65 + $optIndex);
                                                
                                                // Check if this option has an image
                                                $optionImage = $item['question']->option_images[$optIndex] ?? null;
                                                
                                                // Determine what the "value" of this option is (image path or text)
                                                $optionValue = $optionImage ? $optionImage : $option;
                                                
                                                // Check if this is the correct answer
                                                $isCorrect = trim((string) $optionValue) === trim((string) $item['correct_answer']);
                                                
                                                // Check if the student picked this option
                                                $isStudentChoice = $item['student_answer'] !== null 
                                                    && trim((string) $optionValue) === trim((string) $item['student_answer']);
                                            @endphp
                                            
                                            <div class="col-md-6 mb-2">
                                                <div class="p-2 rounded d-flex align-items-center
                                                    @if($isCorrect) bg-success text-white
                                                    @elseif($isStudentChoice) bg-danger text-white
                                                    @else bg-white border
                                                    @endif">
                                                    
                                                    <strong class="me-2">{{ $letter }}.</strong>
                                                    
                                                    @if($optionImage)
                                                        {{-- Display Image --}}
                                                        <img src="{{ asset($optionImage) }}" 
                                                             alt="Option {{ $letter }}" 
                                                             style="max-height: 80px; max-width: 100%; object-fit: contain;" 
                                                             class="rounded bg-white p-1">
                                                    @else
                                                        {{-- Display Text --}}
                                                        {{ $option }}
                                                    @endif
                                                    
                                                    <div class="ms-auto">
                                                        @if($isCorrect)
                                                            <i class="bi bi-check-circle-fill"></i>
                                                        @endif
                                                        @if($isStudentChoice && !$isCorrect)
                                                            <i class="bi bi-x-circle-fill"></i>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <!-- ============ ACTIONS ============ -->
            <div class="text-center mb-4">
                <a href="{{ route('student.exams') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Back to Exams
                </a>
                <a href="{{ route('student.results') }}" class="btn btn-outline-primary">
                    <i class="bi bi-bar-chart"></i> View All Results
                </a>
                @if($attempt->exam->max_attempts > 1 && $attempt->exam->is_available && !$isPending)
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