@extends('layouts.app')

@section('title', 'Grading System')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('page-title', 'Grading System')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admin.grading.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Create Grading Scale
    </a>
</div>

<div class="alert alert-info">
    <i class="bi bi-info-circle"></i>
    The <strong>default</strong> grading scale is used throughout the system to 
    automatically determine student grades based on their scores.
</div>

@if($scales->count() > 0)
    <div class="row g-3">
        @foreach($scales as $scale)
            <div class="col-12 col-lg-6">
                <div class="card h-100 {{ $scale->is_default ? 'border-primary' : '' }}">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">
                                {{ $scale->name }}
                                @if($scale->is_default)
                                    <span class="badge bg-primary ms-1">
                                        <i class="bi bi-star-fill"></i> Default
                                    </span>
                                @endif
                            </h6>
                            @if($scale->description)
                                <small class="text-muted">{{ $scale->description }}</small>
                            @endif
                        </div>
                        <span class="badge bg-secondary">{{ $scale->items_count }} grades</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Score Range</th>
                                        <th>Grade</th>
                                        <th>Remark</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($scale->items as $item)
                                        <tr>
                                            <td>
                                                <code>{{ number_format($item->min_score, 0) }} - {{ number_format($item->max_score, 0) }}</code>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $item->color }}">
                                                    {{ $item->grade }}
                                                </span>
                                            </td>
                                            <td>{{ $item->remark ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white d-flex gap-2">
                        <a href="{{ route('admin.grading.edit', $scale->id) }}" 
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        
                        @if(!$scale->is_default)
                            <form action="{{ route('admin.grading.set-default', $scale->id) }}" 
                                  method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success"
                                        onclick="return confirm('Set this as the default grading scale?')">
                                    <i class="bi bi-star"></i> Set as Default
                                </button>
                            </form>
                            
                            <button type="button" class="btn btn-sm btn-outline-danger ms-auto"
                                    onclick="if(confirm('Delete this grading scale?')) document.getElementById('delete-form-{{ $scale->id }}').submit();">
                                <i class="bi bi-trash"></i>
                            </button>
                            <form id="delete-form-{{ $scale->id }}" 
                                  action="{{ route('admin.grading.delete', $scale->id) }}" 
                                  method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-star fs-1 d-block text-muted"></i>
            <h5 class="mt-3">No Grading Scale Yet</h5>
            <p class="text-muted">
                Create your first grading scale to automatically assign grades to student scores.
            </p>
            <a href="{{ route('admin.grading.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Create Grading Scale
            </a>
        </div>
    </div>
@endif
@endsection