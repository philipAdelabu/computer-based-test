<!-- resources/views/student/view-report-card.blade.php -->
@extends('layouts.app')

@section('title', 'Report Card')

@section('sidebar')
    @include('student.partials.sidebar')
@endsection

@section('page-title', 'Report Card')

@section('content')
<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">{{ $reportCard->class->name }} - Term {{ $reportCard->term }}</h5>
                        <small class="text-muted">Academic Year: {{ $reportCard->academic_year }}</small>
                    </div>
                    <button onclick="window.print()" class="btn btn-outline-primary">
                        <i class="bi bi-printer"></i> Print
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Student Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Student Information</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>{{ $reportCard->student->user->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Admission #:</strong></td>
                                <td>{{ $reportCard->student->admission_number }}</td>
                            </tr>
                            <tr>
                                <td><strong>Class:</strong></td>
                                <td>{{ $reportCard->class->name }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h6>Report Details</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Term:</strong></td>
                                <td>{{ $reportCard->term }}</td>
                            </tr>
                            <tr>
                                <td><strong>Academic Year:</strong></td>
                                <td>{{ $reportCard->academic_year }}</td>
                            </tr>
                            <tr>
                                <td><strong>Date Issued:</strong></td>
                                <td>{{ $reportCard->created_at->format('M d, Y') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Subject Scores -->
                <h6>Subject Scores</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Subject</th>
                                <th>Score</th>
                                <th>Max Score</th>
                                <th>Percentage</th>
                                <th>Grade</th>
                                <th>Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $counter = 1; @endphp
                            @foreach($reportCard->subject_scores as $subjectScore)
                                <tr>
                                    <td>{{ $counter++ }}</td>
                                    <td>{{ $subjectScore['subject'] }}</td>
                                    <td>{{ $subjectScore['score'] }}</td>
                                    <td>{{ $subjectScore['max_score'] }}</td>
                                    <td>{{ number_format($subjectScore['percentage'], 1) }}%</td>
                                    <td>
                                        <span class="badge 
                                            @if($subjectScore['percentage'] >= 80) bg-success
                                            @elseif($subjectScore['percentage'] >= 60) bg-warning
                                            @elseif($subjectScore['percentage'] >= 40) bg-info
                                            @else bg-danger
                                            @endif">
                                            {{ $subjectScore['grade'] }}
                                        </span>
                                    </td>
                                    <td>{{ $subjectScore['remark'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <td colspan="2"><strong>Summary</strong></td>
                                <td><strong>{{ $reportCard->total_score }}</strong></td>
                                <td></td>
                                <td><strong>{{ $reportCard->average_score }}%</strong></td>
                                <td>
                                    <span class="badge 
                                        @if($reportCard->grade == 'A') bg-success
                                        @elseif($reportCard->grade == 'B') bg-primary
                                        @elseif($reportCard->grade == 'C') bg-warning
                                        @else bg-danger
                                        @endif" 
                                        style="font-size: 1rem;">
                                        {{ $reportCard->grade }}
                                    </span>
                                </td>
                                <td>{{ $reportCard->remarks }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <!-- Performance Summary -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <h6>Performance Summary</h6>
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar 
                                    @if($reportCard->average_score >= 70) bg-success
                                    @elseif($reportCard->average_score >= 50) bg-warning
                                    @else bg-danger
                                    @endif" 
                                    role="progressbar" 
                                    style="width: {{ $reportCard->average_score }}%"
                                    aria-valuenow="{{ $reportCard->average_score }}" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                    {{ $reportCard->average_score }}%
                                </div>
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">Position: #{{ $reportCard->position ?? 'N/A' }} out of {{ $reportCard->total_students ?? 'N/A' }} students</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <h6>Grading System</h6>
                            <div class="row small">
                                <div class="col-6">
                                    <span class="badge bg-success">A</span> 80-100% (Excellent)
                                </div>
                                <div class="col-6">
                                    <span class="badge bg-primary">B</span> 70-79% (Very Good)
                                </div>
                                <div class="col-6">
                                    <span class="badge bg-warning">C</span> 60-69% (Good)
                                </div>
                                <div class="col-6">
                                    <span class="badge bg-info">D</span> 50-59% (Fair)
                                </div>
                                <div class="col-6">
                                    <span class="badge bg-danger">E</span> 40-49% (Poor)
                                </div>
                                <div class="col-6">
                                    <span class="badge bg-danger">F</span> Below 40% (Very Poor)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <a href="{{ route('student.report-cards') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Report Cards
                    </a>
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="bi bi-printer"></i> Print Report Card
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
@media print {
    .navbar, .sidebar, .btn, .card-footer {
        display: none !important;
    }
    .col-lg-10 {
        max-width: 100% !important;
        flex: 0 0 100% !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .card-header {
        background: white !important;
    }
}
</style>
@endpush
@endsection