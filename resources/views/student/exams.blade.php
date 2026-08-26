<!-- resources/views/student/exams.blade.php -->
@extends('layouts.app')

@section('title', 'My Exams')

@section('sidebar')
    @include('student.partials.sidebar')
@endsection

@section('page-title', 'Exams')

@section('content')
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <i class="bi bi-clock"></i>
    <strong>All exam times are displayed in your local timezone ({{ config('app.timezone') }}).</strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<!-- Available Exams -->
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
                    
                    <!-- Display schedule information -->
                    @if($exam->schedule_type == 'no_date')
                        <div><i class="bi bi-infinity"></i> Always Available</div>
                    @elseif($exam->schedule_type == 'single_date')
                        <div><i class="bi bi-calendar"></i> {{ $exam->formatted_start_date }}</div>
                        @if($exam->end_date)
                            <div><i class="bi bi-calendar"></i> Until: {{ $exam->formatted_end_date }}</div>
                        @endif
                    @elseif($exam->schedule_type == 'date_range')
                        <div><i class="bi bi-calendar-range"></i> 
                            {{ $exam->available_from ? $exam->available_from->timezone(config('app.timezone'))->format('M d, Y h:i A') : 'N/A' }}
                            - 
                            {{ $exam->available_to ? $exam->available_to->timezone(config('app.timezone'))->format('M d, Y h:i A') : 'N/A' }}
                        </div>
                    @endif
                    
                    @if($exam->max_attempts > 0)
                        <div><i class="bi bi-arrow-repeat"></i> Max {{ $exam->max_attempts }} attempt(s)</div>
                    @else
                        <div><i class="bi bi-infinity"></i> Unlimited attempts</div>
                    @endif
                    @if($exam->passing_score)
                        <div><i class="bi bi-check-circle"></i> Passing: {{ $exam->passing_score }}%</div>
                    @endif
                    @if($exam->instructions)
                        <div><i class="bi bi-info-circle"></i> {{ Str::limit($exam->instructions, 50) }}</div>
                    @endif
                </div>
                
                @php
                    $attempt = $exam->attempts->first();
                    $attemptsCount = $exam->attempts->count();
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
                    @if($exam->max_attempts > 1 && $attemptsCount < $exam->max_attempts)
                        <a href="{{ route('student.exam.take', $exam->id) }}" 
                           class="btn btn-outline-primary w-100 mt-2">
                            <i class="bi bi-arrow-repeat"></i> Retake Exam
                        </a>
                    @endif
                @else
                    <a href="{{ route('student.exam.take', $exam->id) }}" 
                       class="btn btn-primary w-100 mt-3">
                        <i class="bi bi-play-circle"></i> Start Exam
                    </a>
                    @if($attemptsCount > 0)
                        <div class="text-center text-muted mt-2">
                            <small>Attempt {{ $attemptsCount + 1 }} of {{ $exam->max_attempts == 0 ? '∞' : $exam->max_attempts }}</small>
                        </div>
                    @endif
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
                    
                    @if($exam->schedule_type == 'single_date')
                        <div><i class="bi bi-calendar"></i> Starts: {{ $exam->formatted_start_date }}</div>
                        <div class="text-muted">
                            <i class="bi bi-hourglass-split"></i> 
                            Starts {{ $exam->start_date->diffForHumans() }}
                        </div>
                    @elseif($exam->schedule_type == 'date_range')
                        <div><i class="bi bi-calendar-range"></i> 
                            Opens: {{ $exam->available_from ? $exam->available_from->timezone(config('app.timezone'))->format('M d, Y h:i A') : 'N/A' }}
                        </div>
                        <div class="text-muted">
                            <i class="bi bi-hourglass-split"></i> 
                            Opens {{ $exam->available_from->diffForHumans() }}
                        </div>
                    @else
                        <div class="text-muted">Coming soon</div>
                    @endif
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
                        <th>Attempt</th>
                        <th>Score</th>
                        <th>Percentage</th>
                        <th>Result</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($completedExams as $attempt)
                        @php
                            // Only calculate if the attempt is submitted
                            $percentage = 0;
                            $passed = false;
                            $showResult = false;
                            
                            if ($attempt->status === 'submitted' && $attempt->score !== null) {
                                $showResult = true;
                                $percentage = ($attempt->score / $attempt->exam->total_score) * 100;
                                $passed = $attempt->exam->passing_score ? $percentage >= $attempt->exam->passing_score : null;
                            }
                        @endphp
                        <tr>
                            <td>{{ $attempt->exam->title }}</td>
                            <td>{{ $attempt->exam->subject->name }}</td>
                            <td>{{ $attempt->attempt_number ?? 1 }}</td>
                            <td>
                                @if($attempt->status === 'submitted' && $attempt->score !== null)
                                    {{ $attempt->score }}/{{ $attempt->exam->total_score }}
                                @else
                                    <span class="text-muted">In Progress</span>
                                @endif
                            </td>
                            <td>
                                @if($showResult)
                                    {{ number_format($percentage, 1) }}%
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($showResult)
                                    @if($passed === null)
                                        <span class="badge bg-secondary">Completed</span>
                                    @elseif($passed)
                                        <span class="badge bg-success">Passed</span>
                                    @else
                                        <span class="badge bg-danger">Failed</span>
                                    @endif
                                @else
                                    <span class="badge bg-warning">In Progress</span>
                                @endif
                            </td>
                            <td>
                                @if($attempt->status === 'submitted')
                                    <a href="{{ route('student.exam.result', $attempt->id) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    @if($attempt->exam->max_attempts > 1 && $attempt->exam->is_available)
                                        <a href="{{ route('student.exam.take', $attempt->exam->id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-arrow-repeat"></i> Retake
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('student.exam.continue', $attempt->id) }}" 
                                       class="btn btn-sm btn-warning">
                                        <i class="bi bi-play-circle"></i> Continue
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-3">
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