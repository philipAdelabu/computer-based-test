<!-- resources/views/student/results.blade.php -->
@extends('layouts.app')

@section('title', 'My Results')

@section('sidebar')
    @include('student.partials.sidebar')
@endsection

@section('page-title', 'My Results')

@section('content')
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Subject</th>
                        <th>Assessment</th>
                        <th>Score</th>
                        <th>Percentage</th>
                        <th>Grade</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $result)
                        <tr>
                            <td>{{ $loop->iteration + ($results->currentPage() - 1) * $results->perPage() }}</td>
                            <td>{{ $result->subject->name }}</td>
                            <td>{{ $result->assessment_type }}</td>
                            <td>{{ $result->score }}/{{ $result->max_score }}</td>
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
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