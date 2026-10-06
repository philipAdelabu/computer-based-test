
@extends('layouts.app')

@section('title', 'Report Card')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('page-title', 'Report Card')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header col-md-12 col-sm-12 col-xs-12 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">{{ $reportCard->class->name }} - {{ $reportCard->term }}</h5>
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
                    <div class="col-md-6 col-sm-6 col-xs-6">
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
                    <div class="col-md-6 col-sm-6 col-xs-6 text-md-end">
                        <h6>Report Details</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Term:</strong></td>
                                <td>{{ $reportCard->term }}</td>
                            </tr>
                            <tr>
                                <td><strong>Position:</strong></td>
                                <td>#{{ $reportCard->position }} of {{ $reportCard->total_students }}</td>
                            </tr>
                            <tr>
                                <td><strong>Overall Grade:</strong></td>
                                <td>
                                    <span class="badge 
                                        @if($reportCard->grade == 'A') bg-success
                                        @elseif($reportCard->grade == 'B') bg-primary
                                        @elseif($reportCard->grade == 'C') bg-info
                                        @elseif($reportCard->grade == 'D') bg-warning
                                        @else bg-danger
                                        @endif" 
                                        style="font-size: 1.2rem; padding: 0.5rem 1rem;">
                                        {{ $reportCard->grade }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Subject Scores Table -->
                <h6>Subject Performance</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Subject</th>
                                <th class="text-center">Test<br><small>(Max: {{ $reportCard->total_test_max }})</small></th>
                                <th class="text-center">Exam<br><small>(Max: {{ $reportCard->total_exam_max }})</small></th>
                                <th class="text-center">Total</th>
                                <th class="text-center">%</th>
                                <th class="text-center">Grade</th>
                                <th>Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $counter = 1; @endphp
                            @foreach($reportCard->subject_scores as $score)
                                <tr>
                                    <td>{{ $counter++ }}</td>
                                    <td><strong>{{ $score['subject'] }}</strong></td>
                                    <td class="text-center">
                                        {{ $score['test_score'] }}
                                        <small class="text-muted">/{{ $score['test_max'] }}</small>
                                    </td>
                                    <td class="text-center">
                                        {{ $score['exam_score'] }}
                                        <small class="text-muted">/{{ $score['exam_max'] }}</small>
                                    </td>
                                    <td class="text-center">
                                        <strong>{{ $score['total'] }}</strong>
                                        <small class="text-muted">/{{ $score['max'] }}</small>
                                    </td>
                                    <td class="text-center">{{ $score['percentage'] }}%</td>
                                    <td class="text-center">
                                        <span class="badge 
                                            @if($score['grade'] == 'A') bg-success
                                            @elseif($score['grade'] == 'B') bg-primary
                                            @elseif($score['grade'] == 'C') bg-info
                                            @elseif($score['grade'] == 'D') bg-warning
                                            @else bg-danger
                                            @endif">
                                            {{ $score['grade'] }}
                                        </span>
                                    </td>
                                    <td>{{ $score['remark'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <td colspan="2"><strong>TOTAL</strong></td>
                                <td class="text-center">
                                    <strong>{{ $reportCard->total_test_score }}</strong>
                                    <small>/{{ $reportCard->total_test_max }}</small>
                                </td>
                                <td class="text-center">
                                    <strong>{{ $reportCard->total_exam_score }}</strong>
                                    <small>/{{ $reportCard->total_exam_max }}</small>
                                </td>
                                <td class="text-center">
                                    <strong>{{ $reportCard->grand_total }}</strong>
                                    <small>/{{ $reportCard->grand_max }}</small>
                                </td>
                                <td class="text-center">
                                    <strong>{{ $reportCard->overall_percentage }}%</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge 
                                        @if($reportCard->grade == 'A') bg-success
                                        @elseif($reportCard->grade == 'B') bg-primary
                                        @elseif($reportCard->grade == 'C') bg-info
                                        @elseif($reportCard->grade == 'D') bg-warning
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
                
                <!-- Summary -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <h6>Overall Performance</h6>
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar 
                                    @if($reportCard->overall_percentage >= 70) bg-success
                                    @elseif($reportCard->overall_percentage >= 50) bg-warning
                                    @else bg-danger
                                    @endif" 
                                    role="progressbar" 
                                    style="width: {{ $reportCard->overall_percentage }}%">
                                    {{ $reportCard->overall_percentage }}%
                                </div>
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    Total: {{ $reportCard->grand_total }} / {{ $reportCard->grand_max }} | 
                                    Position: #{{ $reportCard->position }} of {{ $reportCard->total_students }}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <h6>Grading System</h6>
                            <div class="row small">
                                <div class="col-6"><span class="badge bg-success">A</span> 80-100% (Excellent)</div>
                                <div class="col-6"><span class="badge bg-primary">B</span> 70-79% (Very Good)</div>
                                <div class="col-6"><span class="badge bg-info">C</span> 60-69% (Good)</div>
                                <div class="col-6"><span class="badge bg-warning">D</span> 50-59% (Fair)</div>
                                <div class="col-6"><span class="badge bg-danger">E</span> 40-49% (Poor)</div>
                                <div class="col-6"><span class="badge bg-danger">F</span> Below 40% (Very Poor)</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <a href="{{ route('teacher.report-cards.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back
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
    .col-lg-12 {
        max-width: 100% !important;
        flex: 0 0 100% !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>
@endpush
@endsection