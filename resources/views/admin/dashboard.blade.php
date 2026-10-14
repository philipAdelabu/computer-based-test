<!-- resources/views/admin/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('page-title', 'Dashboard')

@section('content')

<!-- Replace the stats section -->
<div class="row g-3 mb-4">
    @include('partials.stat-card', ['color' => 'primary',   'icon' => 'bi-people',           'label' => 'Students',   'value' => $totalStudents])
    @include('partials.stat-card', ['color' => 'success',   'icon' => 'bi-person-badge',     'label' => 'Teachers',   'value' => $totalTeachers])
    @include('partials.stat-card', ['color' => 'warning',   'icon' => 'bi-building',         'label' => 'Classes',    'value' => $totalClasses])
    @include('partials.stat-card', ['color' => 'info',      'icon' => 'bi-book',             'label' => 'Subjects',   'value' => $totalSubjects])
</div>

<div class="row g-3 mb-4">
    @include('partials.stat-card', ['color' => 'primary',   'icon' => 'bi-question-circle',  'label' => 'Questions',  'value' => $totalQuestions])
    @include('partials.stat-card', ['color' => 'info',      'icon' => 'bi-file-text',        'label' => 'Tests',      'value' => $totalTests])
    @include('partials.stat-card', ['color' => 'primary',   'icon' => 'bi-file-earmark-text','label' => 'Exams',      'value' => $totalExams])
    @include('partials.stat-card', ['color' => 'success',   'icon' => 'bi-file-earmark-bar-graph','label' => 'Report Cards','value' => $totalReportCards])
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-lightning-charge"></i> Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-2">
                        <a href="{{ route('admin.students.create') }}" class="btn btn-outline-primary w-100 py-3">
                            <i class="bi bi-person-plus fs-4 d-block"></i>
                            Add Student
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.teachers.create') }}" class="btn btn-outline-success w-100 py-3">
                            <i class="bi bi-person-badge fs-4 d-block"></i>
                            Add Teacher
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.exams.create', ['type' => 'test']) }}" class="btn btn-outline-info w-100 py-3">
                            <i class="bi bi-file-text fs-4 d-block"></i>
                            Create Test
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.exams.create', ['type' => 'exam']) }}" class="btn btn-outline-primary w-100 py-3">
                            <i class="bi bi-file-earmark-text fs-4 d-block"></i>
                            Create Exam
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.scores.bulk-upload') }}" class="btn btn-outline-warning w-100 py-3">
                            <i class="bi bi-upload fs-4 d-block"></i>
                            Upload Scores
                        </a>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-secondary w-100 py-3" 
                                data-bs-toggle="modal" data-bs-target="#generateReportModal">
                            <i class="bi bi-file-earmark-bar-graph fs-4 d-block"></i>
                            Report Cards
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Active Assessments & Recent -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-play-circle"></i> Active Assessments</h6>
            </div>
            <div class="card-body p-0">
                @if($activeAssessments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Subject</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activeAssessments as $exam)
                                    <tr>
                                        <td>{{ $exam->title }}</td>
                                        <td>
                                            @if($exam->assessment_type == 'test')
                                                <span class="badge bg-info">Test</span>
                                            @else
                                                <span class="badge bg-primary">Exam</span>
                                            @endif
                                        </td>
                                        <td>{{ $exam->subject->name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-muted mb-0">No active assessments.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-clock-history"></i> Recent Assessments</h6>
            </div>
            <div class="card-body p-0">
                @if($recentAssessments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Created By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAssessments as $exam)
                                    <tr>
                                        <td>{{ $exam->title }}</td>
                                        <td>
                                            @if($exam->assessment_type == 'test')
                                                <span class="badge bg-info">Test</span>
                                            @else
                                                <span class="badge bg-primary">Exam</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $exam->creator->name ?? 'N/A' }}
                                            <br>
                                            <small class="text-muted">{{ ucfirst($exam->created_by_role) }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-muted mb-0">No assessments yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Generate Report Modal -->
<div class="modal fade" id="generateReportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.report-cards.generate') }}" method="POST">
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
                            @foreach(\App\Models\ClassModel::orderBy('name')->get() as $class)
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection