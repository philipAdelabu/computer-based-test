<!-- resources/views/teacher/report-cards/index.blade.php -->
@extends('layouts.app')

@section('title', 'Report Cards')

@section('sidebar')
    @include('teacher.partials.sidebar')
@endsection

@section('page-title', 'Report Cards')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateModal">
        <i class="bi bi-plus-circle"></i> Generate Report Cards
    </button>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Class</th>
                        <th>Term</th>
                        <th>Year</th>
                        <th>Test Score</th>
                        <th>Exam Score</th>
                        <th>Grand Total</th>
                        <th>Grade</th>
                        <th>Position</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportCards as $reportCard)
                        <tr>
                            <td>{{ $loop->iteration + ($reportCards->currentPage() - 1) * $reportCards->perPage() }}</td>
                            <td>
                                <strong>{{ $reportCard->student->user->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $reportCard->student->admission_number }}</small>
                            </td>
                            <td>{{ $reportCard->class->name }}</td>
                            <td>{{ $reportCard->term }}</td>
                            <td>{{ $reportCard->academic_year }}</td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $reportCard->total_test_score }}/{{ $reportCard->total_test_max }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-primary">
                                    {{ $reportCard->total_exam_score }}/{{ $reportCard->total_exam_max }}
                                </span>
                            </td>
                            <td>
                                <strong>{{ $reportCard->grand_total }}/{{ $reportCard->grand_max }}</strong>
                            </td>
                            <td>
                                <span class="badge 
                                    @if($reportCard->grade == 'A') bg-success
                                    @elseif($reportCard->grade == 'B') bg-primary
                                    @elseif($reportCard->grade == 'C') bg-info
                                    @elseif($reportCard->grade == 'D') bg-warning
                                    @else bg-danger
                                    @endif">
                                    {{ $reportCard->grade }}
                                </span>
                            </td>
                            <td>
                                #{{ $reportCard->position ?? 'N/A' }} 
                                <small class="text-muted">of {{ $reportCard->total_students }}</small>
                            </td>
                            <td>
                                <a href="{{ route('teacher.report-cards.show', $reportCard->id) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-4">
                                <i class="bi bi-file-earmark-text fs-1 d-block text-muted"></i>
                                <p class="text-muted mt-2">No report cards generated yet.</p>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#generateModal">
                                    <i class="bi bi-plus-circle"></i> Generate Report Cards
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $reportCards->links() }}
    </div>
</div>

<!-- Generate Modal -->
<div class="modal fade" id="generateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('teacher.report-cards.generate') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Generate Report Cards</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Class</label>
                        <select name="class_id" class="form-select" required>
                            <option value="">-- Select Class --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Term</label>
                        <select name="term" class="form-select" required>
                            <option value="First Term">First Term</option>
                            <option value="Second Term">Second Term</option>
                            <option value="Third Term">Third Term</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Academic Year</label>
                        <input type="number" name="academic_year" class="form-control" 
                               value="{{ date('Y') }}" required>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        Report cards will be generated for all active students in the selected class. 
                        Existing report cards for the same term/year will be updated.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-gear"></i> Generate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection