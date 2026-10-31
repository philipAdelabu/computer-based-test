@extends('layouts.app')
@section('title', 'Edit Class Teacher Remark')
@section('sidebar')
    @include('teacher.partials.sidebar')
@endsection
@section('page-title', 'Class Teacher Remark')

@section('content')
<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="bi bi-chat-left-text text-primary"></i> 
                    Class Teacher Remark for {{ $reportCard->student->user->name }}
                </h6>
            </div>
            <form action="{{ route('teacher.report-cards.remark.update', $reportCard->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="alert alert-info small mb-3">
                        <i class="bi bi-info-circle"></i> 
                        This comment will appear on the student's report card under "Class Teacher's Remark".
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Comment <span class="text-danger">*</span></label>
                        <textarea name="class_teacher_comment" class="form-control @error('class_teacher_comment') is-invalid @enderror" 
                                  rows="5" required>{{ old('class_teacher_comment', $reportCard->class_teacher_comment) }}</textarea>
                        @error('class_teacher_comment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="card-footer bg-white text-end">
                    <a href="{{ route('teacher.report-cards.show', $reportCard->id) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Remark</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection