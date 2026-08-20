<!-- resources/views/teacher/subjects.blade.php -->
@extends('layouts.app')

@section('title', 'My Subjects')

@section('sidebar')
    @include('teacher.partials.sidebar')
@endsection

@section('page-title', 'My Subjects')

@section('content')
<div class="row g-4">
    @forelse($subjects as $subject)
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title">{{ $subject->name }}</h5>
                            <h6 class="text-muted">Code: {{ $subject->code }}</h6>
                        </div>
                        <span class="badge bg-primary">{{ $subject->class->name }}</span>
                    </div>
                    
                    <hr>
                    <div class="row text-center">
                        <div class="col-4">
                            <h6 class="mb-0">{{ $subject->questions->count() }}</h6>
                            <small class="text-muted">Questions</small>
                        </div>
                        <div class="col-4">
                            <h6 class="mb-0">{{ $subject->exams->count() }}</h6>
                            <small class="text-muted">Exams</small>
                        </div>
                        <div class="col-4">
                            <h6 class="mb-0">{{ $subject->class->students->count() }}</h6>
                            <small class="text-muted">Students</small>
                        </div>
                    </div>
                    
                    @if($subject->description)
                        <hr>
                        <p class="small text-muted mb-0">{{ $subject->description }}</p>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-book fs-1 d-block text-muted"></i>
                <p class="text-muted mt-2">You haven't been assigned to any subjects yet.</p>
            </div>
        </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $subjects->links() }}
</div>
@endsection