<!-- resources/views/teacher/questions/edit.blade.php -->
@extends('layouts.app')

@section('title', 'Edit Question')

@section('sidebar')
    @include('teacher.partials.sidebar')
@endsection

@section('page-title', 'Edit Question')

@section('content')
<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('teacher.questions.update', $question->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id', $question->subject_id) == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }} ({{ $subject->class->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Difficulty Level</label>
                            <select name="difficulty" class="form-select @error('difficulty') is-invalid @enderror">
                                <option value="easy" {{ old('difficulty', $question->difficulty) == 'easy' ? 'selected' : '' }}>Easy</option>
                                <option value="medium" {{ old('difficulty', $question->difficulty) == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="hard" {{ old('difficulty', $question->difficulty) == 'hard' ? 'selected' : '' }}>Hard</option>
                            </select>
                            @error('difficulty')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- ============ QUESTION TYPE SELECTION ============ -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Question Type <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="question_type" id="typeObjective" value="objective" 
                                       {{ old('question_type', $question->question_type) == 'objective' ? 'checked' : '' }} onchange="toggleQuestionType()">
                                <label class="form-check-label" for="typeObjective">
                                    <i class="bi bi-ui-checks text-primary"></i> Objective (Multiple Choice)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="question_type" id="typeTheory" value="theory" 
                                       {{ old('question_type', $question->question_type) == 'theory' ? 'checked' : '' }} onchange="toggleQuestionType()">
                                <label class="form-check-label" for="typeTheory">
                                    <i class="bi bi-pencil-square text-success"></i> Theory (Written Answer)
                                </label>
                            </div>
                        </div>
                        @error('question_type')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    
                    <!-- ============ QUESTION TEXT ============ -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Question Text <span class="text-danger">*</span></label>
                        <textarea name="question_text" class="form-control @error('question_text') is-invalid @enderror" 
                                  rows="4" required>{{ old('question_text', $question->question_text) }}</textarea>
                        @error('question_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <!-- ============ QUESTION IMAGE ============ -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Question Image</label>
                        @if($question->image_path)
                            <div class="mb-2">
                                <img src="{{ asset($question->image_path) }}" 
                                     alt="Current Image" 
                                     style="max-height: 150px; max-width: 100%;" 
                                     class="rounded border">
                            </div>
                        @endif
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                        <small class="text-muted">Leave empty to keep current image. Max size: 2MB</small>
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <!-- ============ OBJECTIVE FIELDS (Options & Correct Answer) ============ -->
                    <div id="objectiveFields" class="mb-3">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Options <span class="text-danger">*</span></label>
                            <div id="options-container">
                                @php
                                  // Safely handle old input or existing options
                                        $options = old('options', $question->options);
                                        
                                        // If options is null (like for Theory questions), or not an array, make it an empty array
                                        if (!is_array($options)) {
                                            $options = [];
                                        }
                                        
                                        // Ensure we always have at least 4 slots for the form
                                        while (count($options) < 4) {
                                            $options[] = '';
                                        }

                                     // Set the count for JavaScript safely
                                    $optionCount = count($options);
                                @endphp
                                
                                @foreach($options as $index => $option)
                                    <div class="input-group mb-2">
                                        <span class="input-group-text">{{ chr(65 + $index) }}</span>
                                        <input type="text" name="options[]" class="form-control @error('options.*') is-invalid @enderror option-input" 
                                               placeholder="Option {{ chr(65 + $index) }}" value="{{ $option }}">
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
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Correct Answer <span class="text-danger">*</span></label>
                                <input type="text" name="correct_answer" class="form-control @error('correct_answer') is-invalid @enderror" 
                                       placeholder="Enter the correct answer" value="{{ old('correct_answer', $question->correct_answer) }}">
                                @error('correct_answer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                 <label class="form-label fw-semibold">Score <span class="text-danger">*</span></label>
                                <input type="number" name="score" id="scoreObjectiveInput" class="form-control" 
                                    value="{{ old('score', $question->score) }}" min="1" required>
                                @error('score')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <!-- ============ THEORY SCORE FIELD (Only shows for Theory) ============ -->
                     <div class="mb-3" id="theoryScoreField" style="display: none;">
                        <label class="form-label fw-semibold">Score / Marks <span class="text-danger">*</span></label>
                        <input type="number" name="score" id="scoreTheoryInput" class="form-control" 
                            value="{{ old('score', $question->score) }}" min="1">
                        <small class="text-muted">This is the maximum score the teacher can award for this question.</small>
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
// ============ TOGGLE QUESTION TYPE ============
 function toggleQuestionType() {
    const isTheory = document.getElementById('typeTheory').checked;
    const objectiveFields = document.getElementById('objectiveFields');
    const theoryScoreField = document.getElementById('theoryScoreField');
    const objectiveInputs = objectiveFields.querySelectorAll('input');
    
    // We need to grab the specific score inputs
    const scoreObjective = document.getElementById('scoreObjectiveInput');
    const scoreTheory = document.getElementById('scoreTheoryInput');

    if (isTheory) {
        // Hide Objective Fields and DISABLE them (so they don't submit)
        objectiveFields.style.display = 'none';
        objectiveInputs.forEach(input => {
            input.disabled = true; 
        });

        // Show Theory Score Field
        theoryScoreField.style.display = 'block';
        scoreTheory.required = true;
        scoreTheory.disabled = false;
        
        // Sync the score value
        if (scoreObjective) {
            scoreTheory.value = scoreObjective.value;
        }
    } else {
        // Show Objective Fields and ENABLE them
        objectiveFields.style.display = 'block';
        objectiveInputs.forEach(input => {
            input.disabled = false; 
        });

        // Hide Theory Score Field
        theoryScoreField.style.display = 'none';
        scoreTheory.required = false;
        scoreTheory.disabled = true;

        // Sync the score value
        if (scoreObjective && scoreTheory.value) {
            scoreObjective.value = scoreTheory.value;
        }
    }
}

// ============ OPTIONS MANAGER ============
$(document).ready(function() {
    // Run on page load to set initial state
    toggleQuestionType();

   let optionCount = {{ $optionCount }};

    $('#add-option').on('click', function() {
        if (optionCount >= 6) {
            alert('Maximum 6 options allowed.');
            return;
        }
        
        const letter = String.fromCharCode(65 + optionCount);
        const html = `
            <div class="input-group mb-2">
                <span class="input-group-text">${letter}</span>
                <input type="text" name="options[]" class="form-control option-input" placeholder="Option ${letter}">
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