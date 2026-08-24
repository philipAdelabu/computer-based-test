<!-- resources/views/teacher/exams/show.blade.php -->
@extends('layouts.app')

@section('title', 'Exam Details')

@section('sidebar')
    @include('teacher.partials.sidebar')
@endsection

@section('page-title', 'Exam Details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $exam->title }}</h5>
                    <div>
                        <span class="{{ $exam->status_badge }}">
                            {{ $exam->status_text }}
                        </span>
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
                        <h6>Exam Information</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Subject:</strong></td>
                                <td>{{ $exam->subject->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Class:</strong></td>
                                <td>{{ $exam->subject->class->name }}</td>
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
                                <td><strong>Total Score:</strong></td>
                                <td>{{ $exam->total_score }}</td>
                            </tr>
                            <tr>
                                <td><strong>Created By:</strong></td>
                                <td>{{ $exam->creator->name }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Schedule</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Start Date:</strong></td>
                                <td>{{ $exam->formatted_start_date }}</td>
                            </tr>
                            <tr>
                                <td><strong>End Date:</strong></td>
                                <td>{{ $exam->formatted_end_date }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    @if($exam->is_active)
                                        <span class="badge bg-success">Available Now</span>
                                    @elseif($exam->is_upcoming)
                                        <span class="badge bg-info">Upcoming</span>
                                    @elseif($exam->is_completed)
                                        <span class="badge bg-secondary">Completed</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $exam->status }}</span>
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
                                    <th>#</th>
                                    <th>Question</th>
                                    <th>Score</th>
                                    <th>Difficulty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($exam->questions as $index => $question)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ Str::limit($question->question_text, 100) }}</td>
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($exam->status !== 'completed')
                    <div class="mt-3">
                        <form action="{{ route('teacher.exams.publish', $exam->id) }}" method="POST" class="d-inline">
                            @csrf
                            @if(!$exam->is_published)
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Publish Exam
                                </button>
                            @else
                                <button type="submit" formaction="{{ route('teacher.exams.unpublish', $exam->id) }}" 
                                        class="btn btn-warning">
                                    <i class="bi bi-x-circle"></i> Unpublish Exam
                                </button>
                            @endif
                        </form>
                        <a href="{{ route('teacher.exams.edit', $exam->id) }}" class="btn btn-primary">
                            <i class="bi bi-pencil"></i> Edit Exam
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
                    <h6>Total Students</h6>
                    <h3>{{ $exam->total_students }}</h3>
                </div>
                <div class="mb-3">
                    <h6>Average Score</h6>
                    <h3>{{ number_format($exam->average_score, 1) }}%</h3>
                </div>
                <div class="mb-3">
                    <h6>Pass Rate</h6>
                    <h3>{{ $exam->pass_rate }}%</h3>
                </div>
                <div class="progress mb-3">
                    <div class="progress-bar bg-success" style="width: {{ $exam->pass_rate }}%">
                        {{ $exam->pass_rate }}%
                    </div>
                </div>
            </div>
        </div>

        @if($attempts->count() > 0)
            <div class="card mt-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Recent Attempts</h6>
                </div>
                <div class="card-body p-0">
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
                                        <td>{{ $attempt->student->user->name }}</td>
                                        <td>{{ $attempt->score ?? 'N/A' }}/{{ $exam->total_score }}</td>
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
                    <div class="card-footer bg-white">
                        {{ $attempts->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection