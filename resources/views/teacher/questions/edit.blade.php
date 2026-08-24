<!-- resources/views/teacher/questions/edit.blade.php -->
@extends('layouts.app')

@section('title', 'Edit Question')

@section('sidebar')
    @include('teacher.partials.sidebar')
@endsection

@section('page-title', 'Edit Question')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('teacher.questions.update', $question->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Subject <span class="text-danger">*</span></label>
                        <select name="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                            <option value="">Select Subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id', $question->subject_id) == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }} ({{ $subject->class->name }})
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Question <span class="text-danger">*</span></label>
                        <textarea name="question_text" class="form-control @error('question_text') is-invalid @enderror" 
                                  rows="4" required>{{ old('question_text', $question->question_text) }}</textarea>
                        @error('question_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Question Image</label>
                        @if($question->image_path)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $question->image_path) }}" 
                                     alt="Current Image" 
                                     style="max-height: 150px; max-width: 100%;" 
                                     class="rounded border">
                            </div>
                        @endif
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                        <small class="text-muted">Leave empty to keep current image. Max size: 2MB</small>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Options <span class="text-danger">*</span></label>
                        <div id="options-container">
                            @php
                                $options = old('options', $question->options);
                            @endphp
                            @foreach($options as $index => $option)
                                <div class="input-group mb-2">
                                    <span class="input-group-text">{{ chr(65 + $index) }}</span>
                                    <input type="text" name="options[]" class="form-control @error('options.*') is-invalid @enderror" 
                                           placeholder="Option {{ chr(65 + $index) }}" value="{{ $option }}" required>
                                    @if($index >= 4)
                                        <button type="button" class="btn btn-outline-danger remove-option">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-option">
                            <i class="bi bi-plus-circle"></i> Add Option
                        </button>
                        @error('options.*')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Correct Answer <span class="text-danger">*</span></label>
                            <input type="text" name="correct_answer" class="form-control @error('correct_answer') is-invalid @enderror" 
                                   placeholder="Enter the correct answer" value="{{ old('correct_answer', $question->correct_answer) }}" required>
                            @error('correct_answer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Score <span class="text-danger">*</span></label>
                            <input type="number" name="score" class="form-control @error('score') is-invalid @enderror" 
                                   placeholder="Points for this question" value="{{ old('score', $question->score) }}" min="1" required>
                            @error('score')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Difficulty Level</label>
                        <select name="difficulty" class="form-select @error('difficulty') is-invalid @enderror">
                            <option value="easy" {{ old('difficulty', $question->difficulty) == 'easy' ? 'selected' : '' }}>Easy</option>
                            <option value="medium" {{ old('difficulty', $question->difficulty) == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="hard" {{ old('difficulty', $question->difficulty) == 'hard' ? 'selected' : '' }}>Hard</option>
                        </select>
                        @error('difficulty')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="bi bi-save"></i> Update Question
                        </button>
                        <a href="{{ route('teacher.questions') }}" class="btn btn-secondary px-5">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let optionCount = {{ count(old('options', $question->options)) }};
    
    $('#add-option').on('click', function() {
        if (optionCount >= 6) {
            alert('Maximum 6 options allowed.');
            return;
        }
        
        const letter = String.fromCharCode(65 + optionCount);
        const html = `
            <div class="input-group mb-2">
                <span class="input-group-text">${letter}</span>
                <input type="text" name="options[]" class="form-control" placeholder="Option ${letter}" required>
                <button type="button" class="btn btn-outline-danger remove-option">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        `;
        $('#options-container').append(html);
        optionCount++;
    });
    
    $(document).on('click', '.remove-option', function() {
        if (optionCount <= 4) {
            alert('Minimum 4 options required.');
            return;
        }
        $(this).closest('.input-group').remove();
        optionCount--;
    });
});
</script>
@endpush