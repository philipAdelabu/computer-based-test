@extends('layouts.app')

@section('title', 'My Question Bank')

@section('sidebar')
    @include('teacher.partials.sidebar')  
@endsection

@section('page-title', ' My Question Bank')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('teacher.questions.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>  Add Question
        </a>
        <a href="{{ route('teacher.questions.import') }}" class="btn btn-success">
            <i class="bi bi-upload"></i> Import CSV
        </a>
        <button type="button" class="btn btn-danger" id="bulkDeleteBtn" style="display: none;">
            <i class="bi bi-trash"></i> Delete Selected
        </button>
    </div>
    <div>
        <span class="text-muted">Total Questions: {{ $questions->total() }}</span>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="50">
                            <input type="checkbox" id="selectAll">
                        </th>
                        <th>#</th>
                        <th>Question</th>
                        <th>Subject</th>
                        <th>Options</th>
                        <th>Correct Answer</th>
                        <th>Score</th>
                        <th>Difficulty</th>
                        <th>Actions</th>
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
                                <div class="text-truncate" style="max-width: 200px;">
                                    {{ Str::limit($question->question_text, 60) }}
                                </div>
                                @if($question->image_path)
                                    <i class="bi bi-image text-primary" title="Has image"></i>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $question->subject->name }}</span>
                            </td>
                            <td>
                                <div class="small">
                                    @foreach($question->options as $index => $option)
                                        <div>{{ chr(65 + $index) }}. {{ Str::limit($option, 15) }}</div>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-success">{{ $question->correct_answer }}</span>
                            </td>
                            <td>{{ $question->score }}</td>
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
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteModal{{ $question->id }}">
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
                                                <p>Are you sure you want to delete this question?</p>
                                                <p class="text-muted">{{ $question->question_text }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('teacher.questions.delete', $question->id) }}" method="POST">
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
                            <td colspan="9" class="text-center py-4">
                                <i class="bi bi-inbox fs-1 d-block text-muted"></i>
                                <p class="text-muted mt-2">No questions found. Start building your question bank!</p>
                                <a href="{{ route('teacher.questions.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-circle"></i> Add Your First Question
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
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Select all checkbox
    $('#selectAll').on('change', function() {
        $('.question-checkbox').prop('checked', $(this).prop('checked'));
        updateBulkDeleteButton();
    });
    
    // Individual checkbox change
    $('.question-checkbox').on('change', function() {
        updateBulkDeleteButton();
    });
    
    // Update bulk delete button visibility
    function updateBulkDeleteButton() {
        const checked = $('.question-checkbox:checked');
        const count = checked.length;
        $('#selectedCount').text(count);
        
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
        
        if (confirm(`Are you sure you want to delete ${count} selected questions?`)) {
            const ids = [];
            checked.each(function() {
                ids.push($(this).val());
            });
            
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("teacher.questions.bulk-delete") }}';
            
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
        }
    });
});
</script>
@endpush