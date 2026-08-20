<!-- resources/views/student/exams.blade.php -->
@extends('layouts.app')

@section('title', 'My Exams')

@section('sidebar')
    @include('student.partials.sidebar')
@endsection

@section('page-title', 'Exams')

@section('content')
<h5 class="mb-3">Available Exams</h5>
<div class="row g-4 mb-5">
    @forelse($availableExams as $exam)
        <div class="col-md-4">
            <div class="exam-card">
                <div class="exam-title">{{ $exam->title }}</div>
                <div class="exam-meta">
                    <div><i class="bi bi-book"></i> {{ $exam->subject->name }}</div>
                    <div><i class="bi bi-clock"></i> {{ $exam->duration_minutes }} minutes</div>
                    <div><i class="bi bi-question-circle"></i> {{ $exam->total_questions }} questions</div>
                    <div><i class="bi bi-calendar"></i> {{ $exam->start_date->format('M d, Y h:i A') }}</div>
                </div>
                
                @php
                    $attempt = $exam->attempts->first();
                @endphp
                
                @if($attempt && $attempt->status === 'in_progress')
                    <a href="{{ route('student.exam.continue', $attempt->id) }}" 
                       class="btn btn-warning w-100 mt-3">
                        <i class="bi bi-play-circle"></i> Continue Exam
                    </a>
                @elseif($attempt && $attempt->status === 'submitted')
                    <a href="{{ route('student.exam.result', $attempt->id) }}" 
                       class="btn btn-success w-100 mt-3">
                        <i class="bi bi-check-circle"></i> View Result
                    </a>
                @else
                    <a href="{{ route('student.exam.take', $exam->id) }}" 
                       class="btn btn-primary w-100 mt-3">
                        <i class="bi bi-play-circle"></i> Start Exam
                    </a>
                @endif
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-file-text fs-1 d-block text-muted"></i>
                <p class="text-muted mt-2">No exams available at the moment.</p>
            </div>
        </div>
    @endforelse
</div>

<h5 class="mb-3">Completed Exams</h5>
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Exam</th>
                        <th>Subject</th>
                        <th>Score</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($completedExams as $attempt)
                        <tr>
                            <td>{{ $attempt->exam->title }}</td>
                            <td>{{ $attempt->exam->subject->name }}</td>
                            <td>{{ $attempt->score ?? 'N/A' }}/{{ $attempt->exam->total_score }}</td>
                            <td>
                                <span class="badge bg-success">Completed</span>
                            </td>
                            <td>
                                <a href="{{ route('student.exam.result', $attempt->id) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-3">
                                <p class="text-muted mb-0">No completed exams yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $completedExams->links() }}
    </div>
</div>
@endsection