<!-- resources/views/admin/subjects/index.blade.php -->
@extends('layouts.app')

@section('title', 'Manage Subjects')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('page-title', 'Subjects')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add to Pool
        </a>
        <a href="{{ route('admin.subjects.assign') }}" class="btn btn-success">
            <i class="bi bi-link-45deg"></i> Assign to Class
        </a>
        <!--
        <a href="{{ route('admin.subjects.bulk-assign') }}" class="btn btn-info">
            <i class="bi bi-people"></i> Bulk Assign
        </a>  -->
    </div>
    <div>
        <a href="{{ route('admin.subjects.stats') }}" class="btn btn-outline-secondary">
            <i class="bi bi-graph-up"></i> Stats
        </a>
        <span class="text-muted ms-2">Total: {{ $subjects->total() }}</span>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
              

            <!-- Table -->
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Code</th>
                        <th>Class</th>
                        <th>Teacher</th>
                        <th>Questions</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subjects as $subject)
                        <tr>
                            <td><strong>{{ $subject->name }}</strong></td>
                            <td><code>{{ $subject->code }}</code></td>
                            <td>
                                @if($subject->class_id)
                                    <span class="badge bg-info">{{ $subject->class->name }}</span>
                                @else
                                    <span class="badge bg-secondary">Master Pool</span>
                                @endif
                            </td>
                            <td>
                                @if($subject->teacher_id)
                                    {{ $subject->teacher->name }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $subject->questions()->count() }}</span>
                            </td>
                            <td>
                                @if($subject->class_id)
                                    <span class="badge bg-success">Assigned</span>
                                @else
                                    <span class="badge bg-warning text-dark">In Pool</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.subjects.edit', $subject->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.subjects.delete', $subject->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $subjects->links() }}
    </div>
</div>
@endsection