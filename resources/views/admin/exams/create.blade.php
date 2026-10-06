<!-- resources/views/admin/exams/create.blade.php -->
@extends('layouts.app')

@section('title', 'Create Assessment')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('page-title', 'Create Test or Exam')

@section('content')
<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.exams.store') }}" method="POST" id="examForm">
                    @csrf
                    
                    <!-- Same as teacher create, but with admin routes -->
                    <!-- Copy the entire content from teacher/exams/create.blade.php -->
                    <!-- Change:
                         - form action from teacher.exams.store to admin.exams.store
                         - subject select to include teacher name
                         - any teacher-specific logic
                    -->
                    
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Assessment Type <span class="text-danger">*</span></label>
                            <select name="assessment_type" id="assessmentType" class="form-select" required>
                                <option value="test" {{ old('assessment_type', $defaultType) == 'test' ? 'selected' : '' }}>Test</option>
                                <option value="exam" {{ old('assessment_type', $defaultType) == 'exam' ? 'selected' : '' }}>Exam</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Max Marks <span class="text-danger">*</span></label>
                            <input type="number" name="max_marks" id="maxMarks" class="form-control" 
                                   value="{{ old('max_marks', $defaultType == 'test' ? 30 : 70) }}" min="1" max="200" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Benchmark (%) <span class="text-danger">*</span></label>
                            <input type="number" name="benchmark" id="benchmark" class="form-control" 
                                   value="{{ old('benchmark', 50) }}" min="0" max="100" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Term <span class="text-danger">*</span></label>
                            <select name="term" class="form-select" required>
                                <option value="First Term" {{ old('term') == 'First Term' ? 'selected' : '' }}>First Term</option>
                                <option value="Second Term" {{ old('term') == 'Second Term' ? 'selected' : '' }}>Second Term</option>
                                <option value="Third Term" {{ old('term') == 'Third Term' ? 'selected' : '' }}>Third Term</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Academic Year <span class="text-danger">*</span></label>
                            <input type="number" name="academic_year" class="form-control" 
                                   value="{{ old('academic_year', date('Y')) }}" min="2020" max="2100" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" 
                                   value="{{ old('title') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" id="subjectSelect" class="form-select" required>
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }} ({{ $subject->class->name }}) - 
                                        {{ $subject->teacher->name ?? 'No Teacher' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Copy the rest of the teacher create view from here -->
                    <!-- (Description, Schedule Type, Date fields, Duration, Instructions, Question Selection, Submit button) -->
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Copy the exact same script from teacher/exams/create.blade.php
    // Change route references from teacher to admin
    // e.g., {{ route('teacher.questions.by-subject', '') }} → {{ route('admin.questions.by-subject', '') }}
</script>
@endpush