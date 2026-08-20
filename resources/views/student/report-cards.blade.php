<!-- resources/views/student/report-cards.blade.php -->
@extends('layouts.app')

@section('title', 'Report Cards')

@section('sidebar')
    @include('student.partials.sidebar')
@endsection

@section('page-title', 'Report Cards')

@section('content')
<div class="row g-4">
    @forelse($reportCards as $reportCard)
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title">{{ $reportCard->class->name }}</h5>
                            <h6 class="text-muted">Term {{ $reportCard->term }}</h6>
                        </div>
                        <span class="badge 
                            @if($reportCard->grade == 'A') bg-success
                            @elseif($reportCard->grade == 'B') bg-primary
                            @elseif($reportCard->grade == 'C') bg-warning
                            @else bg-danger
                            @endif" 
                            style="font-size: 1.5rem; padding: 0.5rem 1rem;">
                            {{ $reportCard->grade }}
                        </span>
                    </div>
                    
                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <h6 class="mb-0">{{ $reportCard->total_score }}</h6>
                            <small class="text-muted">Total Score</small>
                        </div>
                        <div class="col-4">
                            <h6 class="mb-0">{{ $reportCard->average_score }}</h6>
                            <small class="text-muted">Average</small>
                        </div>
                        <div class="col-4">
                            <h6 class="mb-0">#{{ $reportCard->position ?? 'N/A' }}</h6>
                            <small class="text-muted">Position</small>
                        </div>
                    </div>
                    
                    <p class="small text-muted">{{ $reportCard->remarks }}</p>
                    
                    <a href="{{ route('student.report-card.view', $reportCard->id) }}" 
                       class="btn btn-primary w-100">
                        <i class="bi bi-eye"></i> View Report Card
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-file-earmark-text fs-1 d-block text-muted"></i>
                <p class="text-muted mt-2">No report cards available yet.</p>
            </div>
        </div>
    @endforelse
</div>
@endsection