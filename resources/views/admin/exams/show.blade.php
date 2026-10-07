<!-- resources/views/admin/exams/show.blade.php -->
@extends('layouts.app')

@section('title', 'Assessment Details')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('page-title', 'Assessment Details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            @if($exam->assessment_type == 'test')
                                <span class="badge bg-info me-2">Test</span>
                            @else
                                <span class="badge bg-primary me-2">Exam</span>
                            @endif
                            {{ $exam->title }}
                        </h5>
                        @if($exam->term)
                            <small class="text-muted">
                                {{ $exam->term }} | {{ $exam->academic_year }} Academic Session
                            </small>
                        @endif
                    </div>
                    <div>
                        {!! $exam->status_badge !!}
                        @if($exam->is_published)
                            <span class="badge bg-success">Published</span>
                        @else
                            <span class="badge bg-secondary">Draft</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Assessment Information</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Type:</strong></td>
                                <td>
                                    @if($exam->assessment_type == 'test')
                                        <span class="badge bg-info">Test (Continuous Assessment)</span>
                                    @else
                                        <span class="badge bg-primary">Exam (Final Examination)</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Subject:</strong></td>
                                <td>{{ $exam->subject->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Class:</strong></td>
                                <td>{{ $exam->subject->class->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Teacher:</strong></td>
                                <td>{{ $exam->subject->teacher->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Duration:</strong></td>
                                <td>{{ $exam->duration_minutes }} minutes</td>
                            </tr>
                            <tr>
                                <td><strong>Total Questions:</strong></td>
                                <td>{{ $exam->total_questions }}</td>
                            </tr>
                            <tr>
                                <td><strong>Raw Total Score:</strong></td>
                                <td>{{ $exam->total_score }} points</td>
                            </tr>
                            <tr>
                                <td><strong>Max Marks:</strong></td>
                                <td><strong>{{ $exam->max_marks }}</strong> marks</td>
                            </tr>
                            <tr>
                                <td><strong>Benchmark:</strong></td>
                                <td>{{ $exam->benchmark }}% to pass</td>
                            </tr>
                            <tr>
                                <td><strong>Max Attempts:</strong></td>
                                <td>
                                    @if($exam->max_attempts > 0)
                                        {{ $exam->max_attempts }}
                                    @else
                                        Unlimited
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Created By:</strong></td>
                                <td>
                                    {{ $exam->creator->name ?? 'N/A' }}
                                    <span class="badge bg-light text-dark">
                                        {{ ucfirst($exam->created_by_role) }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Schedule</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Schedule Type:</strong></td>
                                <td>
                                    @if($exam->schedule_type == 'no_date')
                                        <span class="badge bg-info">Always Available</span>
                                    @elseif($exam->schedule_type == 'single_date')
                                        <span class="badge bg-primary">Single Date</span>
                                    @else
                                        <span class="badge bg-warning">Date Range</span>
                                    @endif
                                </td>
                            </tr>
                            @if($exam->schedule_type == 'single_date')
                                <tr>
                                    <td><strong>Start Date:</strong></td>
                                    <td>{{ $exam->formatted_start_date }}</td>
                                </tr>
                                @if($exam->end_date)
                                    <tr>
                                        <td><strong>End Date:</strong></td>
                                        <td>{{ $exam->formatted_end_date }}</td>
                                    </tr>
                                @endif
                            @elseif($exam->schedule_type == 'date_range')
                                <tr>
                                    <td><strong>Available From:</strong></td>
                                    <td>{{ $exam->available_from ? $exam->available_from->timezone(config('app.timezone'))->format('F d, Y h:i A') : 'Not Set' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Available To:</strong></td>
                                    <td>{{ $exam->available_to ? $exam->available_to->timezone(config('app.timezone'))->format('F d, Y h:i A') : 'Not Set' }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    @if($exam->is_available)
                                        <span class="badge bg-success">Available Now</span>
                                    @elseif($exam->is_upcoming)
                                        <span class="badge bg-info">Upcoming</span>
                                    @elseif($exam->is_completed)
                                        <span class="badge bg-secondary">Completed</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($exam->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @if($exam->instructions)
                                <tr>
                                    <td><strong>Instructions:</strong></td>
                                    <td>{{ $exam->instructions }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                @if($exam->description)
                    <div class="mb-3">
                        <h6>Description</h6>
                        <p>{{ $exam->description }}</p>
                    </div>
                @endif

                <div class="mb-3">
                    <h6>Questions ({{ $exam->questions->count() }})</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>Question</th>
                                    <th style="width: 70px;">Score</th>
                                    <th style="width: 90px;">Difficulty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($exam->questions as $index => $question)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            {{ Str::limit($question->question_text, 100) }}
                                                @if($question->has_image)
                                                    <a href="{{ $question->image_url }}" target="_blank">
                                                        <i class="bi bi-image text-primary" title="Has image"></i>
                                                    </a>
                                                @endif
                                        </td>
                                        <td>{{ $question->score }}</td>
                                        <td>
                                            <span class="badge 
                                                @if($question->difficulty == 'easy') bg-success
                                                @elseif($question->difficulty == 'medium') bg-warning
                                                @else bg-danger
                                                @endif">
                                                {{ ucfirst($question->difficulty) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">
                                            No questions attached to this assessment.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($exam->status !== 'completed')
                    <div class="mt-3">
                        @if(!$exam->is_published)
                            <form action="{{ route('admin.exams.publish', $exam->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Publish Assessment
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.exams.unpublish', $exam->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-x-circle"></i> Unpublish Assessment
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('admin.exams.edit', $exam->id) }}" class="btn btn-primary">
                            <i class="bi bi-pencil"></i> Edit Assessment
                        </a>
                        <a href="{{ route('admin.exams') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0">Statistics</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-muted small">Total Students</h6>
                    <h3 class="mb-0">{{ $exam->total_students }}</h3>
                </div>
                <div class="mb-3">
                    <h6 class="text-muted small">Average Score</h6>
                    <h3 class="mb-0">{{ number_format($exam->average_score, 1) }}%</h3>
                </div>
                <div class="mb-3">
                    <h6 class="text-muted small">Pass Rate</h6>
                    <h3 class="mb-0">{{ $exam->pass_rate }}%</h3>
                </div>
                <div class="progress mb-3" style="height: 20px;">
                    <div class="progress-bar bg-success" 
                         style="width: {{ $exam->pass_rate }}%">
                        {{ $exam->pass_rate }}%
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="bi bi-clipboard-check"></i> Recent Attempts
                </h6>
            </div>
            <div class="card-body p-0">
                @if($attempts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Score</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attempts as $attempt)
                                    <tr>
                                        <td>
                                            <small>
                                                {{ $attempt->student->user->name ?? 'N/A' }}
                                                <br>
                                                <span class="text-muted">
                                                    {{ $attempt->student->admission_number ?? '' }}
                                                </span>
                                            </small>
                                        </td>
                                        <td>
                                            <small>
                                                {{ $attempt->score ?? 'N/A' }}/{{ $exam->total_score }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge 
                                                @if($attempt->status == 'submitted') bg-success
                                                @elseif($attempt->status == 'in_progress') bg-warning
                                                @else bg-secondary
                                                @endif">
                                                {{ ucfirst($attempt->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($attempts->hasPages())
                        <div class="card-footer bg-white">
                            {{ $attempts->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-inbox fs-1 d-block text-muted"></i>
                        <p class="text-muted small mb-0 mt-2">No attempts yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection