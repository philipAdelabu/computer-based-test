<!-- resources/views/admin/classes/index.blade.php -->
@extends('layouts.app')

@section('title', 'Manage Classes')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('page-title', 'Classes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.classes.create') }}" class="btn btn-primary">
            <i class="bi bi-building-add"></i> Add Class
        </a>
        <a href="{{ route('admin.classes.admit') }}" class="btn btn-success">
            <i class="bi bi-arrow-up-circle"></i> Admit Students
        </a>
    </div>
    <div>
        <span class="text-muted">Total Classes: {{ $classes->total() }}</span>
    </div>
</div>

<div class="row g-4">
    @forelse($classes as $class)
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title">{{ $class->name }}</h5>
                            <h6 class="text-muted">Code: {{ $class->code }}</h6>
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('admin.classes.edit', $class->id) }}" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" 
                                    class="btn btn-sm btn-outline-danger" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteModal{{ $class->id }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h6 class="mb-0">{{ $class->students_count }}</h6>
                                <small class="text-muted">Students</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div>
                                <h6 class="mb-0">{{ $class->subjects_count }}</h6>
                                <small class="text-muted">Subjects</small>
                            </div>
                        </div>
                    </div>
                    
                   <!-- In the class card, update the admit button -->
                    @if($class->students_count > 0)
                        <div class="mt-3">
                            <a href="{{ route('admin.classes.admit') }}?class={{ $class->id }}" 
                            class="btn btn-success btn-sm w-100">
                                <i class="bi bi-arrow-up"></i> Admit Students from this Class
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Delete Modal -->
            <div class="modal fade" id="deleteModal{{ $class->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Delete Class</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete <strong>{{ $class->name }}</strong>?</p>
                            <p class="text-danger"><i class="bi bi-exclamation-triangle"></i> This will also delete all associated subjects and students.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <form action="{{ route('admin.classes.delete', $class->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-building fs-1 d-block text-muted"></i>
                <p class="text-muted mt-2">No classes found. Create your first class!</p>
            </div>
        </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $classes->links() }}
</div>
@endsection