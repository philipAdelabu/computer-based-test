@extends('layouts.app')
@section('title', 'Bulk Traits & Skills')
@section('sidebar')
    @include('teacher.partials.sidebar')
@endsection
@section('page-title', 'Bulk Traits & Skills')

@section('content')
<div class="row">
    <div class="col-lg-9 mx-auto">
        
        <!-- STEP 1: Select Class and Term -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-funnel"></i> Step 1: Select Class and Term</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('teacher.report-cards.bulk-traits') }}" class="row g-3 align-items-end">
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

        <!-- STEP 2: Apply Bulk Traits -->
        @if($selectedClassId && $reportCards->count() > 0)
            <form action="{{ route('teacher.report-cards.bulk-traits.store') }}" method="POST">
                @csrf
                <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                <input type="hidden" name="term" value="{{ $selectedTerm }}">
                <input type="hidden" name="academic_year" value="{{ $selectedYear }}">
                
                <div class="alert alert-warning small mb-3">
                    <i class="bi bi-exclamation-triangle"></i> 
                    Leave a field as "Do Not Change" to keep the student's existing rating. Ratings you set here will be applied to all <strong>{{ $reportCards->count() }}</strong> students.
                </div>

                <div class="row g-4 mb-4">
                    <!-- AFFECTIVE TRAITS -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">Affective Traits</h6>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Trait</th>
                                            <th class="text-center" style="width: 150px;">Apply Rating</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($affectiveList as $trait)
                                            <tr>
                                                <td class="align-middle">{{ $trait }}</td>
                                                <td class="text-center">
                                                    <select name="affective_traits[{{ $trait }}]" class="form-select form-select-sm">
                                                        <option value="">Do Not Change</option>
                                                        @for($i = 5; $i >= 1; $i--)
                                                            <option value="{{ $i }}">{{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- PSYCHOMOTOR SKILLS -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">Psychomotor Skills</h6>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Skill</th>
                                            <th class="text-center" style="width: 150px;">Apply Rating</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($psychomotorList as $skill)
                                            <tr>
                                                <td class="align-middle">{{ $skill }}</td>
                                                <td class="text-center">
                                                    <select name="psychomotor_skills[{{ $skill }}]" class="form-select form-select-sm">
                                                        <option value="">Do Not Change</option>
                                                        @for($i = 5; $i >= 1; $i--)
                                                            <option value="{{ $i }}">{{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <span class="text-muted">
                            Applying to: <strong>{{ $reportCards->count() }} students</strong>
                        </span>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-lightning-charge"></i> Apply Bulk Traits
                        </button>
                    </div>
                </div>
            </form>
        @elseif($selectedClassId)
            <div class="alert alert-info">No report cards found for this class, term, and year.</div>
        @endif
    </div>
</div>
@endsection