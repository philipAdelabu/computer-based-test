<!-- resources/views/admin/subjects/bulk-assign.blade.php -->
@extends('layouts.app')

@section('title', 'Bulk Assign Subjects')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('page-title', 'Bulk Assign Subjects to Class and Teacher')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.subjects.bulk-assign.process') }}" method="POST">
                    @csrf
                    
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
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Subjects <span class="text-danger">*</span></label>
                        <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                            @foreach($subjects as $subject)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           name="subject_ids[]" 
                                           value="{{ $subject->id }}" 
                                           id="subject_{{ $subject->id }}"
                                           {{ (is_array(old('subject_ids')) && in_array($subject->id, old('subject_ids'))) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="subject_{{ $subject->id }}">
                                        {{ $subject->name }} ({{ $subject->code }})
                                        <span class="text-muted">
                                            - Current: {{ $subject->class->name ?? 'No Class' }} | 
                                            {{ $subject->teacher->name ?? 'No Teacher' }}
                                        </span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('subject_ids')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Select one or more subjects to assign</small>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        This will update the class and teacher for all selected subjects.
                        Any existing assignments will be overwritten.
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-info px-5">
                            <i class="bi bi-people"></i> Bulk Assign
                        </button>
                        <a href="{{ route('admin.subjects') }}" class="btn btn-secondary px-5">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection