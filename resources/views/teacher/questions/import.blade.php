<!-- resources/views/teacher/questions/import.blade.php -->
@extends('layouts.app')

@section('title', 'Import Questions')

@section('sidebar')
    @include('teacher.partials.sidebar')
@endsection

@section('page-title', 'Import Questions from CSV')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('teacher.questions.import.csv') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Subject <span class="text-danger">*</span></label>
                        <select name="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                            <option value="">Select Subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }} ({{ $subject->class->name }})
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">CSV File <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" 
                               accept=".csv,.txt" required>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle"></i> Supported formats: .csv (Max: 5MB)
                        </small>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6><i class="bi bi-file-spreadsheet"></i> CSV Format Instructions</h6>
                        <p class="mb-0">Your CSV file should have these columns:</p>
                        <ul class="mb-0 mt-1">
                            <li><strong>question</strong> - The question text (Required)</li>
                            <li><strong>option_a</strong> - Option A (Required)</li>
                            <li><strong>option_b</strong> - Option B (Required)</li>
                            <li><strong>option_c</strong> - Option C (Required)</li>
                            <li><strong>option_d</strong> - Option D (Required)</li>
                            <li><strong>correct_answer</strong> - Correct answer (Required)</li>
                            <li><strong>score</strong> - Points (Optional, defaults to 1)</li>
                            <li><strong>difficulty</strong> - easy, medium, or hard (Optional)</li>
                        </ul>
                        <div class="mt-2">
                            <a href="{{ route('teacher.questions.download-template') }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-download"></i> Download Template
                            </a>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success px-5">
                            <i class="bi bi-upload"></i> Import Questions
                        </button>
                        <a href="{{ route('teacher.questions') }}" class="btn btn-secondary px-5">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection