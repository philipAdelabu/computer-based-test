<!-- resources/views/admin/subjects/assign.blade.php -->
@extends('layouts.app')

@section('title', 'Assign Subject')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('page-title', 'Assign Subject to Class and Teacher')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.subjects.assign.process') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Subject <span class="text-danger">*</span></label>
                        <select name="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                            <option value="">Select Subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }} ({{ $subject->code }}) 
                                    - Current: {{ $subject->class->name ?? 'No Class' }} | 
                                    {{ $subject->teacher->name ?? 'No Teacher' }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Assign to Class <span class="text-danger">*</span></label>
                            <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Assign to Teacher <span class="text-danger">*</span></label>
                            <select name="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror" required>
                                <option value="">Select Teacher</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->name }} ({{ $teacher->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('teacher_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        This will assign the selected subject to the chosen class and teacher.
                        The subject will be available for students in that class.
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success px-5">
                            <i class="bi bi-check-circle"></i> Assign Subject
                        </button>
                        <a href="{{ route('admin.subjects') }}" class="btn btn-secondary px-5">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection