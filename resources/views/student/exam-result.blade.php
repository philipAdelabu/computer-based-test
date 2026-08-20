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
                    <div class="mb-4">
                        <div class="display-1 mb-3">
                            @if($result->percentage >= 70)
                                <i class="bi bi-emoji-smile text-success"></i>
                            @elseif($result->percentage >= 50)
                                <i class="bi bi-emoji-neutral text-warning"></i>
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
                                <h6 class="text-muted">Grade</h6>
                                <h3>
                                    <span class="badge 
                                        @if($result->percentage >= 80) bg-success
                                        @elseif($result->percentage >= 60) bg-warning
                                        @else bg-danger
                                        @endif" 
                                        style="font-size: 2rem; padding: 0.5rem 1.5rem;">
                                        {{ $result->grade }}
                                    </span>
                                </h3>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert 
                        @if($result->percentage >= 70) alert-success
                        @elseif($result->percentage >= 50) alert-warning
                        @else alert-danger
                        @endif">
                        <i class="bi 
                            @if($result->percentage >= 70) bi-check-circle
                            @elseif($result->percentage >= 50) bi-exclamation-triangle
                            @else bi-x-circle
                            @endif me-2"></i>
                        {{ $result->remarks }}
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('student.exams') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-left"></i> Back to Exams
                        </a>
                        <a href="{{ route('student.results') }}" class="btn btn-outline-primary">
                            <i class="bi bi-bar-chart"></i> View All Results
                        </a>
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