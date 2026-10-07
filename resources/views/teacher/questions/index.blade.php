
@extends('layouts.app')

@section('title', 'Question Bank')

@section('sidebar')
    @include('teacher.partials.sidebar')
@endsection

@section('page-title', 'Question Bank')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('teacher.questions.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Question
        </a>
        <a href="{{ route('teacher.questions.import') }}" class="btn btn-success">
            <i class="bi bi-upload"></i> Import CSV
        </a>
        <button type="button" class="btn btn-danger" id="bulkDeleteBtn" style="display: none;">
            <i class="bi bi-trash"></i> Delete Selected (<span id="bulkCount">0</span>)
        </button>
    </div>
    <div>
        <span class="text-muted">
            <i class="bi bi-collection"></i>
            Total: <strong>{{ number_format($totalQuestions) }}</strong> questions
            @if($filteredCount != $totalQuestions)
                | Filtered: <strong>{{ number_format($filteredCount) }}</strong>
            @endif
        </span>
    </div>
</div>

<!-- ============ FILTERS ============ -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Class</label>
                <select name="class_id" id="classFilter" class="form-select">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" 
                                {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Subject</label>
                <select name="subject_id" id="subjectFilter" class="form-select">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" 
                                data-class="{{ $subject->class_id }}"
                                {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }} 
                            @if($subject->class)
                                ({{ $subject->class->name }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Difficulty</label>
                <select name="difficulty" class="form-select">
                    <option value="">All</option>
                    <option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>Easy</option>
                    <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Hard</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Search</label>
                <div class="input-group">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search questions..." 
                           value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                @if(request()->hasAny(['class_id', 'subject_id', 'difficulty', 'search']))
                    <a href="{{ route('teacher.questions') }}" class="btn btn-outline-secondary w-100" title="Clear filters">
                        <i class="bi bi-x-circle"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

@if($selectedSubject || $selectedClass)
    <!-- ============ CONTEXT HEADER ============ -->
    <div class="alert alert-info d-flex justify-content-between align-items-center">
        <div>
            <i class="bi bi-funnel-fill"></i>
            Showing questions for:
            @if($selectedClass)
                <span class="badge bg-primary">{{ $selectedClass->name }}</span>
            @endif
            @if($selectedSubject)
                <i class="bi bi-arrow-right"></i>
                <span class="badge bg-success">{{ $selectedSubject->name }}</span>
            @endif
        </div>
        <a href="{{ route('teacher.questions') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-x"></i> Clear Filter
        </a>
    </div>
@endif

<!-- ============ GROUPED VIEW (NO FILTERS) ============ -->
@if(!request()->hasAny(['class_id', 'subject_id', 'difficulty', 'search']))
    @php
        $groupedQuestions = $classes->mapWithKeys(function($class) {
            return [$class->id => [
                'class' => $class,
                'subjects' => $class->subjects->map(function($subject) {
                    return [
                        'subject' => $subject,
                        'count' => $subject->questions_count ?? 0,
                    ];
                })
            ]];
        });
    @endphp

    <div class="row g-4">
        @foreach($groupedQuestions as $classData)
            <div class="col-12">
                <div class="card class-card">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-building"></i>
                                {{ $classData['class']->name }}
                                <small class="ms-2 opacity-75">
                                    ({{ $classData['subjects']->count() }} subjects)
                                </small>
                            </h5>
                            <div>
                                @php
                                    $classTotal = $classData['subjects']->sum('count');
                                @endphp
                                <span class="badge bg-light text-dark">
                                    {{ number_format($classTotal) }} questions total
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($classData['subjects']->count() > 0)
                            <div class="row g-3">
                                @foreach($classData['subjects'] as $subjectData)
                                    @php $subject = $subjectData['subject']; @endphp
                                    <div class="col-md-4 col-lg-3">
                                        <a href="{{ route('teacher.questions', ['subject_id' => $subject->id]) }}"
                                           class="subject-card text-decoration-none">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="subject-icon">
                                                    <i class="bi bi-book-half"></i>
                                                </div>
                                                <span class="badge bg-{{ $subjectData['count'] > 0 ? 'success' : 'secondary' }}">
                                                    {{ $subjectData['count'] }} Qs
                                                </span>
                                            </div>
                                            <h6 class="mb-1 text-dark">{{ $subject->name }}</h6>
                                            <small class="text-muted d-block">
                                                <code>{{ $subject->code }}</code>
                                            </small>
                                            @if($subject->teacher)
                                                <small class="text-muted d-block mt-1">
                                                    <i class="bi bi-person"></i> 
                                                    {{ Str::limit($subject->teacher->name, 20) }}
                                                </small>
                                            @endif
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="bi bi-inbox fs-1 d-block text-muted"></i>
                                <p class="text-muted mb-0">No subjects in this class yet.</p>
                                <a href="{{ route('teacher.subjects.create') }}" class="btn btn-sm btn-primary mt-2">
                                    <i class="bi bi-plus"></i> Add Subject
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

        @if($classes->isEmpty())
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-inbox fs-1 d-block text-muted"></i>
                        <h5 class="mt-3">No Classes Found</h5>
                        <p class="text-muted">Create classes and subjects to start building your question bank.</p>
                        <a href="{{ route('teacher.classes.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Create Class
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

<!-- ============ LIST VIEW (WITH FILTERS) ============ -->
@else
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th style="width: 40px;">#</th>
                            <th>Question</th>
                            <th style="width: 120px;">Class</th>
                            <th style="width: 140px;">Subject</th>
                            <th style="width: 200px;">Options</th>
                            <th style="width: 100px;">Answer</th>
                            <th style="width: 60px;">Score</th>
                            <th style="width: 90px;">Difficulty</th>
                            <th style="width: 130px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($questions as $question)
                            <tr>
                                <td>
                                    <input type="checkbox" class="question-checkbox" value="{{ $question->id }}">
                                </td>
                                <td>{{ $loop->iteration + ($questions->currentPage() - 1) * $questions->perPage() }}</td>
                                <td>
                                    <div class="d-flex">
                                        @if($question->has_image)
                                            <a href="{{ $question->image_url }}" target="_blank" class="me-2">
                                                <img src="{{ $question->image_url }}" 
                                                     style="width: 40px; height: 40px; object-fit: cover;"
                                                     class="rounded border">
                                            </a>
                                        @endif
                                        <div>
                                            {{ Str::limit($question->question_text, 100) }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $question->subject->class->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $question->subject->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="small">
                                        @foreach($question->options as $index => $option)
                                            <div>
                                                <strong>{{ chr(65 + $index) }}.</strong> 
                                                {{ Str::limit($option, 25) }}
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        {{ $question->correct_answer }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $question->score }}</strong>
                                </td>
                                <td>
                                    @if($question->difficulty == 'easy')
                                        <span class="badge bg-success">Easy</span>
                                    @elseif($question->difficulty == 'medium')
                                        <span class="badge bg-warning">Medium</span>
                                    @else
                                        <span class="badge bg-danger">Hard</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('teacher.questions.edit', $question->id) }}" 
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal{{ $question->id }}"
                                                title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $question->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Delete Question</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Delete this question?</p>
                                                    <p class="text-muted small">{{ Str::limit($question->question_text, 200) }}</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('admin.questions.delete', $question->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <i class="bi bi-inbox fs-1 d-block text-muted"></i>
                                    <p class="text-muted mt-2">No questions found matching your criteria.</p>
                                    <a href="{{ route('admin.questions') }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-x"></i> Clear Filters
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
            <div>
                <span id="selectedCount">0</span> questions selected
            </div>
            <div>
                {{ $questions->links() }}
            </div>
        </div>
    </div>
@endif
@endsection

@push('styles')
<style>
.subject-card {
    display: block;
    padding: 15px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    background: white;
    transition: all 0.25s ease;
    height: 100%;
}
.subject-card:hover {
    border-color: #4e73df;
    background: #f8f9ff;
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(78, 115, 223, 0.15);
}
.subject-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e7f0ff;
    color: #4e73df;
    border-radius: 8px;
    font-size: 1.2rem;
}
.class-card {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
.class-card .card-header {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%) !important;
    border: none;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Class filter -> Subject filter cascade
    $('#classFilter').on('change', function() {
        const classId = $(this).val();
        const subjectSelect = $('#subjectFilter');
        
        subjectSelect.find('option').each(function() {
            const optClassId = $(this).data('class');
            if (!classId || !optClassId || optClassId == classId) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        
        // Reset subject selection if hidden
        const selected = subjectSelect.find('option:selected');
        if (selected.data('class') && classId && selected.data('class') != classId) {
            subjectSelect.val('');
        }
    });
    
    // Trigger on page load
    $('#classFilter').trigger('change');
    
    // Select all checkbox
    $('#selectAll').on('change', function() {
        $('.question-checkbox').prop('checked', $(this).prop('checked'));
        updateBulkDelete();
    });
    
    // Individual checkbox
    $(document).on('change', '.question-checkbox', function() {
        updateBulkDelete();
    });
    
    function updateBulkDelete() {
        const count = $('.question-checkbox:checked').length;
        $('#selectedCount').text(count);
        $('#bulkCount').text(count);
        
        if (count > 0) {
            $('#bulkDeleteBtn').show();
        } else {
            $('#bulkDeleteBtn').hide();
        }
    }
    
    // Bulk delete
    $('#bulkDeleteBtn').on('click', function() {
        const checked = $('.question-checkbox:checked');
        const count = checked.length;
        
        if (count === 0) return;
        
        if (!confirm(`Delete ${count} selected questions? This cannot be undone.`)) return;
        
        const ids = [];
        checked.each(function() {
            ids.push($(this).val());
        });
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.questions.bulk-delete") }}';
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);
        
        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'DELETE';
        form.appendChild(method);
        
        ids.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'question_ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    });
});
</script>
@endpush