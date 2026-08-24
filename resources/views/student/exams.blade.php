<!-- resources/views/student/exams.blade.php -->
@extends('layouts.app')

@section('title', 'My Exams')

@section('sidebar')
    @include('student.partials.sidebar')
@endsection

@section('page-title', 'Exams')

@section('content')
<!-- Available Exams -->
 <div class="alert alert-info alert-dismissible fade show" role="alert">
    <i class="bi bi-clock"></i>
    <strong>All exam times are displayed in your local timezone ({{ config('app.timezone') }}).</strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<h5 class="mb-3">Available Exams</h5>
<div class="row g-4 mb-5">
    @forelse($availableExams as $exam)
           <div class="col-md-4">
        <div class="exam-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="exam-title">{{ $exam->title }}</div>
                <span class="badge bg-success">Available</span>
            </div>
            <div class="exam-meta">
                <div><i class="bi bi-book"></i> {{ $exam->subject->name }}</div>
                <div><i class="bi bi-clock"></i> {{ $exam->duration_minutes }} minutes</div>
                <div><i class="bi bi-question-circle"></i> {{ $exam->total_questions }} questions</div>
                <div><i class="bi bi-calendar"></i> Ends: {{ $exam->formatted_end_date }}</div>
                <div class="text-muted">
                    <i class="bi bi-hourglass-split"></i> 
                    <span class="exam-timer" data-end="{{ $exam->end_date_iso }}">
                        {{ $exam->remaining_time_human }}
                    </span>
                </div>
                @if($exam->instructions)
                    <div><i class="bi bi-info-circle"></i> {{ Str::limit($exam->instructions, 50) }}</div>
                @endif
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
            <div class="text-center py-4">
                <i class="bi bi-file-text fs-1 d-block text-muted"></i>
                <p class="text-muted">No exams available at the moment.</p>
            </div>
        </div>
    @endforelse
</div>

<!-- Upcoming Exams -->
<h5 class="mb-3">Upcoming Exams</h5>
<div class="row g-4 mb-5">
    @forelse($upcomingExams as $exam)
        <div class="col-md-4">
            <div class="exam-card" style="border-left: 4px solid #ffc107;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="exam-title">{{ $exam->title }}</div>
                    <span class="badge bg-warning">Upcoming</span>
                </div>
                <div class="exam-meta">
                    <div><i class="bi bi-book"></i> {{ $exam->subject->name }}</div>
                    <div><i class="bi bi-clock"></i> {{ $exam->duration_minutes }} minutes</div>
                    <div><i class="bi bi-calendar"></i> Starts: {{ $exam->formatted_start_date }}</div>
                    <div class="text-muted">
                        <i class="bi bi-hourglass-split"></i> 
                        {{ $exam->start_date->diffForHumans() }}
                    </div>
                </div>
                <div class="mt-3 text-center text-muted">
                    <small><i class="bi bi-info-circle"></i> This exam will be available on the scheduled date</small>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center py-3">
                <p class="text-muted">No upcoming exams.</p>
            </div>
        </div>
    @endforelse
</div>

<!-- Completed Exams -->
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
                        <th>Percentage</th>
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
                                @if($attempt->score)
                                    {{ number_format(($attempt->score / $attempt->exam->total_score) * 100, 1) }}%
                                @else
                                    N/A
                                @endif
                            </td>
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
                            <td colspan="6" class="text-center py-3">
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


@push('scripts')
<script>
// Timer countdown for exams
$(document).ready(function() {
    function updateTimers() {
        $('.exam-timer').each(function() {
            const endDate = new Date($(this).data('end'));
            const now = new Date();
            const diff = (endDate - now) / 1000; // difference in seconds
            
            if (diff <= 0) {
                $(this).text('Expired');
                return;
            }
            
            const hours = Math.floor(diff / 3600);
            const minutes = Math.floor((diff % 3600) / 60);
            const seconds = Math.floor(diff % 60);
            
            let timeString = '';
            if (hours > 0) {
                timeString = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            } else {
                timeString = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            }
            
            $(this).text(timeString);
        });
    }
    
    // Update timers every second
    setInterval(updateTimers, 1000);
    updateTimers();
});
</script>
@endpush