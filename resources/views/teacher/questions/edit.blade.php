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
                <form action="{{ route('teacher.questions.update', $question->id) }}" method="POST" enctype="multipart/form-data" id="questionForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Row 1: Subject & Difficulty -->
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
                            <select name="difficulty" class="form-select">
                                <option value="easy" {{ old('difficulty', $question->difficulty) == 'easy' ? 'selected' : '' }}>Easy</option>
                                <option value="medium" {{ old('difficulty', $question->difficulty) == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="hard" {{ old('difficulty', $question->difficulty) == 'hard' ? 'selected' : '' }}>Hard</option>
                            </select>
                        </div>
                    </div>

                    <!-- Question Type -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Question Type <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="question_type" id="typeObjective" value="objective" 
                                       {{ old('question_type', $question->question_type) == 'objective' ? 'checked' : '' }} onchange="toggleQuestionType()">
                                <label class="form-check-label" for="typeObjective"><i class="bi bi-ui-checks text-primary"></i> Objective</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="question_type" id="typeTheory" value="theory" 
                                       {{ old('question_type', $question->question_type) == 'theory' ? 'checked' : '' }} onchange="toggleQuestionType()">
                                <label class="form-check-label" for="typeTheory"><i class="bi bi-pencil-square text-success"></i> Theory</label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Question Text -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Question Text <span class="text-danger">*</span></label>
                        <textarea name="question_text" class="form-control rich-editor" rows="4" required>{{ old('question_text', $question->question_text) }}</textarea>
                    </div>
                    
                    <!-- Question Image -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Question Image</label>
                        @if($question->image_path)
                            <div class="mb-2">
                                <img src="{{ asset($question->image_path) }}" class="rounded border" style="max-height: 150px;">
                            </div>
                        @endif
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    
                    <!-- OBJECTIVE FIELDS -->
                    <div id="objectiveFields">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-semibold mb-0">Options <span class="text-danger">*</span></label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="toggleImageOptions" onchange="toggleOptionMode()" 
                                           {{ $question->option_images && count($question->option_images) > 0 ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="toggleImageOptions">Use Images</label>
                                </div>
                            </div>
                            
                            <div id="options-container">
                                @php
                                    $options = old('options', $question->options ?? []);
                                    if(!is_array($options)) $options = [];
                                    while (count($options) < 4) $options[] = '';
                                    
                                    $optionImages = old('option_images', $question->option_images ?? []);
                                    if(!is_array($optionImages)) $optionImages = [];
                                @endphp
                                
                                @foreach($options as $index => $option)
                                    @php $existingImage = $optionImages[$index] ?? null; @endphp
                                    <div class="option-row mb-2">
                                        <div class="input-group">
                                            <span class="input-group-text">{{ chr(65 + $index) }}</span>
                                            <input type="text" name="options[]" class="form-control option-text-input" 
                                                   placeholder="Option {{ chr(65 + $index) }}" value="{{ $option }}" onkeyup="refreshDropdown()">
                                            <input type="file" name="option_images[{{ $index }}]" class="form-control option-image-input d-none" accept="image/*">
                                            
                                            @if($index >= 4)
                                                <button type="button" class="btn btn-outline-danger remove-option"><i class="bi bi-x"></i></button>
                                            @endif
                                        </div>
                                        
                                        @if($existingImage)
                                            <div class="mt-2 ms-5 d-flex align-items-center gap-3 p-2 border rounded bg-light option-existing-image">
                                                <img src="{{ asset($existingImage) }}" class="rounded border" style="height: 50px; width: 50px; object-fit: cover;">
                                                <div class="form-check ms-auto mb-0">
                                                    <input class="form-check-input" type="checkbox" name="remove_images[{{ $index }}]" value="1" id="remove_img_{{ $index }}">
                                                    <label class="form-check-label text-danger small" for="remove_img_{{ $index }}">Remove Image</label>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-option"><i class="bi bi-plus-circle"></i> Add Option</button>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Correct Answer <span class="text-danger">*</span></label>
                                <select name="correct_answer" id="correctAnswerSelect" class="form-select">
                                    <!-- Populated by JS -->
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Score <span class="text-danger">*</span></label>
                                <input type="number" name="score" class="form-control" value="{{ old('score', $question->score ?? 1) }}" min="1" required>
                            </div>
                        </div>
                    </div>

                    <!-- THEORY FIELDS -->
                    <div id="theoryFields" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Score / Marks <span class="text-danger">*</span></label>
                            <input type="number" name="score_theory" id="scoreTheory" class="form-control" value="{{ old('score', $question->score ?? 1) }}" min="1">
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5"><i class="bi bi-save"></i> Update Question</button>
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
    // 1. Initialize state on load
    document.addEventListener('DOMContentLoaded', function() {
        const hasImages = document.getElementById('toggleImageOptions').checked;
        if (hasImages) {
            toggleOptionMode();
        } else {
            refreshDropdown();
        }
        toggleQuestionType();
    });

    // 2. Toggle Theory vs Objective
    function toggleQuestionType() {
        const isTheory = document.getElementById('typeTheory').checked;
        const objectiveFields = document.getElementById('objectiveFields');
        const theoryFields = document.getElementById('theoryFields');
        const objectiveScore = objectiveFields.querySelector('input[name="score"]');
        const theoryScore = document.getElementById('scoreTheory');

        if (isTheory) {
            objectiveFields.style.display = 'none';
            objectiveFields.querySelectorAll('input, select').forEach(el => el.disabled = true);
            
            theoryFields.style.display = 'block';
            theoryScore.disabled = false;
            theoryScore.required = true;
            theoryScore.value = objectiveScore.value; // Sync score
        } else {
            objectiveFields.style.display = 'block';
            objectiveFields.querySelectorAll('input, select').forEach(el => el.disabled = false);
            
            theoryFields.style.display = 'none';
            theoryScore.disabled = true;
            theoryScore.required = false;
            objectiveScore.value = theoryScore.value; // Sync score
        }
    }

    // 3. Toggle Text vs Image Options
    function toggleOptionMode() {
        const useImages = document.getElementById('toggleImageOptions').checked;
        const textInputs = document.querySelectorAll('.option-text-input');
        const imageInputs = document.querySelectorAll('.option-image-input');
        const existingPreviews = document.querySelectorAll('.option-existing-image');
        
        if (useImages) {
            textInputs.forEach(input => { input.classList.add('d-none'); input.disabled = true; });
            imageInputs.forEach(input => { input.classList.remove('d-none'); input.disabled = false; });
            existingPreviews.forEach(preview => preview.classList.remove('d-none'));
        } else {
            textInputs.forEach(input => { input.classList.remove('d-none'); input.disabled = false; });
            imageInputs.forEach(input => { input.classList.add('d-none'); input.disabled = true; });
            existingPreviews.forEach(preview => preview.classList.add('d-none'));
        }
        refreshDropdown();
    }

    // 4. Refresh Dropdown
    function refreshDropdown() {
        const select = document.getElementById('correctAnswerSelect');
        if (!select) return;
        
        const isImageMode = document.getElementById('toggleImageOptions').checked;
        const textInputs = document.querySelectorAll('.option-text-input');
        
        // Get the saved correct answer from PHP (it's the index: 0, 1, 2, 3)
        const currentValue = "{{ old('correct_answer', $question->correct_answer) }}";
        
        select.innerHTML = '<option value="">-- Select Correct Option --</option>';
        
        textInputs.forEach((input, index) => {
            const letter = String.fromCharCode(65 + index);
            let display = isImageMode ? `${letter}. (Image Option)` : `${letter}. ${input.value || '(Empty)'}`;
            
            const opt = document.createElement('option');
            opt.value = index; // Always submit the index (0, 1, 2...)
            opt.textContent = display;
            
            // If the saved correct answer matches this index, select it
            if (opt.value == currentValue) opt.selected = true;
            select.appendChild(opt);
        });
    }

    // 5. Options Manager
    $(document).ready(function() {
        let optionCount = {{ is_array(old('options', $question->options)) ? count(old('options', $question->options)) : 4 }};

        $('#add-option').on('click', function() {
            if (optionCount >= 6) { alert('Maximum 6 options allowed.'); return; }
            
            const letter = String.fromCharCode(65 + optionCount);
            const html = `
                <div class="option-row mb-2">
                    <div class="input-group">
                        <span class="input-group-text">${letter}</span>
                        <input type="text" name="options[]" class="form-control option-text-input" placeholder="Option ${letter}" onkeyup="refreshDropdown()">
                        <input type="file" name="option_images[${optionCount}]" class="form-control option-image-input d-none" accept="image/*">
                        <button type="button" class="btn btn-outline-danger remove-option"><i class="bi bi-x"></i></button>
                    </div>
                </div>
            `;
            $('#options-container').append(html);
            optionCount++;
            refreshDropdown();
        });
        
        $(document).on('click', '.remove-option', function() {
            if (optionCount <= 4) { alert('Minimum 4 options required.'); return; }
            $(this).closest('.option-row').remove();
            optionCount--;
            refreshDropdown();
        });
    });
</script>
@endpush

@include('partials.rich-editor')