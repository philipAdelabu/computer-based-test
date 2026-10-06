<!-- resources/views/student/results.blade.php -->
@extends('layouts.app')

@section('title', 'My Results')

@section('sidebar')
    @include('student.partials.sidebar')
@endsection

@section('page-title', 'My Results')

@section('content')
<!-- Filter Tabs -->
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link {{ request('type') != 'test' && request('type') != 'exam' ? 'active' : '' }}" 
           href="{{ route('student.results') }}">
            All Results
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('type') == 'test' ? 'active' : '' }}" 
           href="{{ route('student.results') }}?type=test">
            <i class="bi bi-file-text text-info"></i> Tests Only
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('type') == 'exam' ? 'active' : '' }}" 
           href="{{ route('student.results') }}?type=exam">
            <i class="bi bi-file-earmark-text text-primary"></i> Exams Only
        </a>
    </li>
</ul>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Assessment</th>
                        <th>Type</th>
                        <th>Subject</th>
                        <th>Score</th>
                        <th>Percentage</th>
                        <th>Grade</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $result)
                        <tr>
                            <td>{{ $loop->iteration + ($results->currentPage() - 1) * $results->perPage() }}</td>
                            <td>
                                <strong>{{ $result->exam->title ?? 'N/A' }}</strong>
                                @if($result->exam)
                                    <br>
                                    <small class="text-muted">
                                        Max Marks: {{ $result->exam->max_marks }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                @if($result->exam && $result->exam->assessment_type == 'test')
                                    <span class="badge bg-info">Test</span>
                                @elseif($result->exam)
                                    <span class="badge bg-primary">Exam</span>
                                @else
                                    <span class="badge bg-secondary">{{ $result->assessment_type }}</span>
                                @endif
                            </td>
                            <td>{{ $result->subject->name ?? 'N/A' }}</td>
                            <td>
                                <strong>{{ $result->score }}</strong>/{{ $result->max_score }}
                            </td>
                            <td>{{ number_format($result->percentage, 1) }}%</td>
                            <td>
                                <span class="badge 
                                    @if($result->percentage >= 80) bg-success
                                    @elseif($result->percentage >= 60) bg-warning
                                    @elseif($result->percentage >= 40) bg-info
                                    @else bg-danger
                                    @endif">
                                    {{ $result->grade }}
                                </span>
                            </td>
                            <td>{{ $result->assessment_date->format('M d, Y') }}</td>
                            <td>
                                @if($result->exam_id)
                                    @php
                                        $attempt = \App\Models\ExamAttempt::where('exam_id', $result->exam_id)
                                            ->where('student_id', $result->student_id)
                                            ->where('status', 'submitted')
                                            ->latest()
                                            ->first();
                                    @endphp
                                    @if($attempt)
                                        <a href="{{ route('student.exam.result', $attempt->id) }}" 
                                           class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="bi bi-bar-chart fs-1 d-block text-muted"></i>
                                <p class="text-muted mt-2">No results found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $results->links() }}
    </div>
</div>
@endsection