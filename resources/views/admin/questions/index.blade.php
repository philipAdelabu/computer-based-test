<!-- resources/views/admin/questions/index.blade.php -->
@extends('layouts.app')

@section('title', 'Manage Questions')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('page-title', 'Question Bank')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.questions.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Question
        </a>
        <a href="{{ route('admin.questions.import') }}" class="btn btn-success">
            <i class="bi bi-upload"></i> Import from Excel
        </a>
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
                        <th>#</th>
                        <th>Question</th>
                        <th>Subject</th>
                        <th>Options</th>
                        <th>Correct Answer</th>
                        <th>Score</th>
                        <th>Difficulty</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($questions as $question)
                        <tr>
                            <td>{{ $loop->iteration + ($questions->currentPage() - 1) * $questions->perPage() }}</td>
                            <td>
                                <div class="text-truncate" style="max-width: 200px;">
                                    {{ Str::limit($question->question_text, 50) }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $question->subject->name ?? 'N/A' }}
                                </span>
                                <br>
                                <small class="text-muted">{{ $question->subject->class->name ?? '' }}</small>
                            </td>
                            <td>
                                <div class="small">
                                    @foreach($question->options as $index => $option)
                                        <div>{{ chr(65 + $index) }}. {{ Str::limit($option, 20) }}</div>
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
                                   @if($question->has_image)
                                        <a href="{{ $question->image_url }}" target="_blank" title="View image">
                                            <img src="{{ $question->image_url }}" 
                                                alt="Question Image" 
                                                class="rounded border"
                                                style="width: 50px; height: 50px; object-fit: cover;">
                                        </a>
                                    @else
                                        <span class="text-muted small">No image</span>
                                    @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.questions.edit', $question->id) }}" 
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
                            <td colspan="9" class="text-center py-4">
                                <i class="bi bi-inbox fs-1 d-block text-muted"></i>
                                <p class="text-muted mt-2">No questions found. Start by adding your first question!</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $questions->links() }}
    </div>
</div>
@endsection