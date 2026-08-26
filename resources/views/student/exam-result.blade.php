<!-- resources/views/student/exam-result.blade.php -->
@extends('layouts.app')

@section('title', 'Exam Result')

@section('sidebar')
    @include('student.partials.sidebar')
@endsection

@section('page-title', 'Exam Result')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body text-center">
                @if($result)
                    @php
                        $passed = $result->exam->passing_score ? $result->percentage >= $result->exam->passing_score : null;
                    @endphp
                    
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
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted">Score</h6>
                                <h3>{{ $result->score }}/{{ $result->max_score }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted">Percentage</h6>
                                <h3>{{ number_format($result->percentage, 1) }}%</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted">Status</h6>
                                <h3>
                                    @if($passed === null)
                                        <span class="badge bg-primary" style="font-size: 1.5rem; padding: 0.5rem 1.5rem;">
                                            Completed
                                        </span>
                                    @elseif($passed)
                                        <span class="badge bg-success" style="font-size: 1.5rem; padding: 0.5rem 1.5rem;">
                                            Passed
                                        </span>
                                    @else
                                        <span class="badge bg-danger" style="font-size: 1.5rem; padding: 0.5rem 1.5rem;">
                                            Failed
                                        </span>
                                    @endif
                                </h3>
                            </div>
                        </div>
                    </div>
                    
                    @if($passed !== null)
                        <div class="alert 
                            @if($passed) alert-success
                            @else alert-danger
                            @endif">
                            <i class="bi 
                                @if($passed) bi-check-circle
                                @else bi-x-circle
                                @endif me-2"></i>
                            @if($passed)
                                Congratulations! You passed the exam.
                            @else
                                You did not meet the passing score of {{ $result->exam->passing_score }}%.
                            @endif
                        </div>
                    @endif
                    
                    <div class="mt-4">
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
                    <div class="py-5">
                        <i class="bi bi-file-text fs-1 d-block text-muted mb-3"></i>
                        <h5>Result not found</h5>
                        <p class="text-muted">This exam result is not available.</p>
                        <a href="{{ route('student.exams') }}" class="btn btn-primary mt-3">
                            <i class="bi bi-arrow-left"></i> Back to Exams
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection