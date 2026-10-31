@extends('layouts.app')
@section('title', 'Bulk Class Teacher Remarks')
@section('sidebar')
    @include('teacher.partials.sidebar')
@endsection
@section('page-title', 'Bulk Class Teacher Remarks')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <!-- STEP 1: Select Class and Term -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-funnel"></i> Step 1: Select Class and Term</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('teacher.report-cards.bulk-remark') }}" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label">Class</label>
                        <select name="class_id" class="form-select" required>
                            <option value="">-- Select Class --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ $selectedClassId == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Term</label>
                        <select name="term" class="form-select" required>
                            <option value="First Term" {{ $selectedTerm == 'First Term' ? 'selected' : '' }}>First Term</option>
                            <option value="Second Term" {{ $selectedTerm == 'Second Term' ? 'selected' : '' }}>Second Term</option>
                            <option value="Third Term" {{ $selectedTerm == 'Third Term' ? 'selected' : '' }}>Third Term</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Year</label>
                        <input type="number" name="academic_year" class="form-control" 
                               value="{{ $selectedYear ?? date('Y') }}" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Load</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- STEP 2: Apply Bulk Comment -->
        @if($selectedClassId && $reportCards->count() > 0)
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-chat-left-text"></i> Step 2: Enter Comment</h6>
                </div>
                <form action="{{ route('teacher.report-cards.bulk-remark.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                    <input type="hidden" name="term" value="{{ $selectedTerm }}">
                    <input type="hidden" name="academic_year" value="{{ $selectedYear }}">
                    
                    <div class="card-body">
                        <div class="alert alert-warning small mb-3">
                            <i class="bi bi-exclamation-triangle"></i> 
                            This will <strong>overwrite</strong> the class teacher's comment for all 
                            <strong>{{ $reportCards->count() }}</strong> students shown below.
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Bulk Comment <span class="text-danger">*</span></label>
                            <textarea name="class_teacher_comment" class="form-control" rows="4" required></textarea>
                        </div>

                        <!-- Preview of affected students -->
                        <h6 class="mt-4 mb-2">Affected Students:</h6>
                        <div class="table-responsive border rounded">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Admission #</th>
                                        <th>Current Comment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reportCards as $rc)
                                        <tr>
                                            <td>{{ $rc->student->user->name }}</td>
                                            <td>{{ $rc->student->admission_number }}</td>
                                            <td>
                                                <small class="text-muted">{{ Str::limit($rc->class_teacher_comment, 50) ?: '—' }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-end">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-lightning-charge"></i> Apply to {{ $reportCards->count() }} Students
                        </button>
                    </div>
                </form>
            </div>
        @elseif($selectedClassId)
            <div class="alert alert-info">No report cards found for this class, term, and year.</div>
        @endif
    </div>
</div>
@endsection