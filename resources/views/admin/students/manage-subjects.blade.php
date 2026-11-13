@extends('layouts.app')
@section('title', 'Manage Student Subjects')
@section('sidebar')
    @include('admin.partials.sidebar')
@endsection
@section('page-title', 'Manage Subjects')

@section('content')
<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Subjects for {{ $student->user->name }}</h5>
                <small class="text-muted">Class: {{ $student->class->name }}</small>
            </div>
            <form action="{{ route('admin.students.subjects.update', $student->id) }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="alert alert-info small">
                        <i class="bi bi-info-circle"></i> Check the subjects this student is offering. Only checked subjects will appear on their dashboard and report card.
                    </div>
                    
                    <div class="row g-3">
                        @foreach($subjects as $subject)
                            <div class="col-md-6">
                                <div class="form-check p-2 border rounded">
                                    <input class="form-check-input ms-1 me-2" type="checkbox" 
                                           name="subjects[]" value="{{ $subject->id }}" 
                                           id="sub_{{ $subject->id }}"
                                           {{ in_array($subject->id, $enrolledSubjectIds) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="sub_{{ $subject->id }}">
                                        {{ $subject->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer bg-white text-end">
                    <a href="{{ route('admin.students.show', $student->id) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Subjects</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection