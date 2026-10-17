@extends('layouts.app')

@section('title', 'Edit Report Card')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('page-title', 'Edit Report Card')

@section('content')

<div class="card mb-3 bg-light">
    <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h6 class="mb-0">
                <i class="bi bi-magic text-primary"></i> 
                Smart Auto-Fill
            </h6>
            <small class="text-muted">
                Automatically generate grades, traits, and comments based on performance
            </small>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary btn-sm" id="autoFillBtn">
                <i class="bi bi-lightning-charge-fill"></i> Auto-Fill Empty Fields
            </button>
            <button type="button" class="btn btn-outline-warning btn-sm" id="autoFillOverwriteBtn">
                <i class="bi bi-arrow-repeat"></i> Auto-Fill & Overwrite
            </button>
        </div>
    </div>
</div>

<!-- Add hidden form for auto-fill -->
<form id="autoFillForm" action="{{ route('admin.report-cards.auto-fill', $reportCard->id) }}" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="overwrite" id="overwriteFlag" value="0">
</form>


<form action="{{ route('admin.report-cards.update', $reportCard->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Student Info -->
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Student Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="">-- Auto (from student) --</option>
                                <option value="Male" {{ $reportCard->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ $reportCard->gender == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                            <small class="text-muted">
                                Auto-filled from student record. Change only to override.
                            </small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Age</label>
                            <input type="number" name="age" class="form-control" value="{{ $reportCard->age }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">House</label>
                            <input type="text" name="house" class="form-control" value="{{ $reportCard->house }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Students in Class</label>
                            <input type="number" name="no_in_class" class="form-control" value="{{ $reportCard->no_in_class }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Session</label>
                            <input type="text" name="session" class="form-control" 
                                   value="{{ $reportCard->session }}" placeholder="e.g., 2025/2026">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Result Analysis -->
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Result Analysis</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Max Class Score</label>
                            <input type="number" step="0.01" name="max_class_score" class="form-control" 
                                   value="{{ $reportCard->max_class_score }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Min Class Score</label>
                            <input type="number" step="0.01" name="min_class_score" class="form-control" 
                                   value="{{ $reportCard->min_class_score }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Marks Obtainable</label>
                            <input type="number" name="marks_obtainable" class="form-control" 
                                   value="{{ $reportCard->marks_obtainable }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance -->
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Attendance</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Times School Opened</label>
                            <input type="number" name="times_school_opened" class="form-control" 
                                   value="{{ $reportCard->times_school_opened }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Times Present</label>
                            <input type="number" name="times_present" class="form-control" 
                                   value="{{ $reportCard->times_present }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Affective Traits -->
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Affective Traits (Rating: 1 - 5)</h6>
                </div>
                <div class="card-body">
                    @foreach($defaultAffective as $trait)
                        @php
                            $current = $reportCard->affective_traits[$trait] ?? 4;
                        @endphp
                        <div class="row align-items-center mb-2">
                            <div class="col-md-5">
                                <label class="form-label mb-0">{{ $trait }}</label>
                            </div>
                            <div class="col-md-7">
                                <div class="btn-group w-100" role="group">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" 
                                               class="btn-check" 
                                               name="affective_traits[{{ $trait }}]" 
                                               id="aff_{{ Str::slug($trait) }}_{{ $i }}" 
                                               value="{{ $i }}"
                                               {{ (int) $current === $i ? 'checked' : '' }}>
                                        <label class="btn btn-outline-primary btn-sm" 
                                               for="aff_{{ Str::slug($trait) }}_{{ $i }}">
                                            {{ $i }}
                                        </label>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Psychomotor Skills -->
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Psychomotor Skills (Rating: 1 - 5)</h6>
                </div>
                <div class="card-body">
                    @foreach($defaultPsychomotor as $skill)
                        @php
                            $current = $reportCard->psychomotor_skills[$skill] ?? 4;
                        @endphp
                        <div class="row align-items-center mb-2">
                            <div class="col-md-5">
                                <label class="form-label mb-0">{{ $skill }}</label>
                            </div>
                            <div class="col-md-7">
                                <div class="btn-group w-100" role="group">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" 
                                               class="btn-check" 
                                               name="psychomotor_skills[{{ $skill }}]" 
                                               id="psy_{{ Str::slug($skill) }}_{{ $i }}" 
                                               value="{{ $i }}"
                                               {{ (int) $current === $i ? 'checked' : '' }}>
                                        <label class="btn btn-outline-success btn-sm" 
                                               for="psy_{{ Str::slug($skill) }}_{{ $i }}">
                                            {{ $i }}
                                        </label>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Comments -->
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Comments</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Class Teacher's Name</label>
                            <input type="text" name="class_teacher_name" class="form-control" 
                                   value="{{ $reportCard->class_teacher_name }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Principal's Name</label>
                            <input type="text" name="principal_name" class="form-control" 
                                   value="{{ $reportCard->principal_name }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Class Teacher's Comment</label>
                            <textarea name="class_teacher_comment" class="form-control" rows="2">{{ $reportCard->class_teacher_comment }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Principal's Comment</label>
                            <textarea name="principal_comment" class="form-control" rows="2">{{ $reportCard->principal_comment }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vacation & Resumption -->
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Vacation & Resumption</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Vacation Date</label>
                            <input type="date" name="vacation_date" class="form-control" 
                                   value="{{ $reportCard->vacation_date ? $reportCard->vacation_date->format('Y-m-d') : '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Resumption Date</label>
                            <input type="date" name="resumption_date" class="form-control" 
                                   value="{{ $reportCard->resumption_date ? $reportCard->resumption_date->format('Y-m-d') : '' }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-body text-center">
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="bi bi-save"></i> Save Changes
                    </button>
                    <a href="{{ route('admin.report-cards.show', $reportCard->id) }}" 
                       class="btn btn-secondary px-5">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.getElementById('autoFillBtn').addEventListener('click', function() {
    if (!confirm('Auto-fill empty fields based on student performance?')) return;
    document.getElementById('overwriteFlag').value = '0';
    document.getElementById('autoFillForm').submit();
});

document.getElementById('autoFillOverwriteBtn').addEventListener('click', function() {
    if (!confirm('This will OVERWRITE all existing traits and comments. Continue?')) return;
    document.getElementById('overwriteFlag').value = '1';
    document.getElementById('autoFillForm').submit();
});
</script>
@endpush