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
                         <div class="mb-3" id="objectiveOptionsWrapper">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-semibold mb-0">Options <span class="text-danger">*</span></label>
                                     <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="toggleImageOptions" 
                                            onchange="toggleOptionMode()" 
                                            {{ $question->option_images && count($question->option_images) > 0 ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="toggleImageOptions">Use Images for Options</label>
                                    </div>
                                </div>
                          <div class="mb-3">
                        
                             <div id="options-container">
                                    @php
                                        $options = old('options', $question->options ?? ['', '', '', '']);
                                        if(!is_array($options)) $options = [];
                                        while (count($options) < 4) $options[] = '';
                                        
                                        $optionImages = old('option_images', $question->option_images ?? []);
                                    @endphp
                                    
                                    @foreach($options as $index => $option)
                                        @php
                                            $existingImage = $optionImages[$index] ?? null;
                                        @endphp
                                        <div class="option-row mb-2">
                                            <div class="input-group">
                                                <span class="input-group-text">{{ chr(65 + $index) }}</span>
                                                
                                                <!-- Text Input -->
                                                <input type="text" name="options[]" class="form-control option-text-input @error('options.*') is-invalid @enderror" 
                                                    placeholder="Option {{ chr(65 + $index) }}" value="{{ $option }}">
                                                
                                                <!-- Image Input (Hidden by default) -->
                                                <input type="file" name="option_images[{{ $index }}]" class="form-control option-image-input d-none" accept="image/*">
                                                
                                                @if($index >= 4)
                                                    <button type="button" class="btn btn-outline-danger remove-option"><i class="bi bi-x"></i></button>
                                                @endif
                                            </div>
                                            
                                            <!-- Display the existing image (if any) -->
                                            @if($existingImage)
                                                <div class="mt-2 ms-5 d-flex align-items-center gap-3 p-2 border rounded bg-light option-existing-image">
                                                    <img src="{{ asset($existingImage) }}" alt="Option {{ chr(65 + $index) }}" 
                                                        class="rounded border" style="height: 50px; width: 50px; object-fit: cover;">
                                                    <small class="text-muted">Current Image</small>
                                                    
                                                    <div class="form-check ms-auto mb-0">
                                                        <input class="form-check-input" type="checkbox" name="remove_images[{{ $index }}]" value="1" id="remove_img_{{ $index }}">
                                                        <label class="form-check-label text-danger small" for="remove_img_{{ $index }}">
                                                            Remove Image
                                                        </label>
                                                    </div>
                                                </div>
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
                        
                          <!-- ============ CORRECT ANSWER SECTION ============ -->
                            <div class="row mb-3" id="objectiveCorrectAnswerWrapper">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Correct Answer <span class="text-danger">*</span></label>
                                    <select name="correct_answer" id="correctAnswerSelect" class="form-select" required>
                                        <option value="">-- Select Correct Option --</option>
                                        @if(is_array($options))
                                            @foreach($options as $index => $opt)
                                                @php
                                                    // If editing an image question, the value stored in DB is the image path
                                                    $val = $optionImages[$index] ?? $opt;
                                                @endphp
                                                <option value="{{ $val }}" {{ old('correct_answer', $question->correct_answer ?? '') == $val ? 'selected' : '' }}>
                                                    Option {{ chr(65 + $index) }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                              
                            </div>
                            <div class="col-md-6" id="scoreWrapper">
                                    <label class="form-label fw-semibold">Score <span class="text-danger">*</span></label>
                                    <input type="number" name="score" class="form-control" value="{{ old('score', $question->score ?? 1) }}" min="1" required>
                                </div>
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

   let optionCount = {{ is_array(old('options', $question->options)) ? count(old('options', $question->options)) : 4 }};

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

    document.addEventListener('DOMContentLoaded', function() {
        // Check if the question has option images
        const hasImages = {{ $question->option_images && count($question->option_images) > 0 ? 'true' : 'false' }};
        
        if (hasImages) {
            document.getElementById('toggleImageOptions').checked = true;
            toggleOptionMode(); // Trigger the toggle function
        }
        
        toggleQuestionType(); // Initialize question type
        refreshDropdown();    // Initialize dropdown
    });

    function toggleOptionMode() {
    const useImages = document.getElementById('toggleImageOptions').checked;
    const textInputs = document.querySelectorAll('.option-text-input');
    const imageInputs = document.querySelectorAll('.option-image-input');
    const existingPreviews = document.querySelectorAll('.option-existing-image');
    
    if (useImages) {
        // Hide text inputs, show image inputs and previews
        textInputs.forEach(input => { input.classList.add('d-none'); input.disabled = true; });
        imageInputs.forEach(input => { input.classList.remove('d-none'); input.disabled = false; });
        existingPreviews.forEach(preview => preview.classList.remove('d-none'));
    } else {
        // Show text inputs, hide image inputs and previews
        textInputs.forEach(input => { input.classList.remove('d-none'); input.disabled = false; });
        imageInputs.forEach(input => { input.classList.add('d-none'); input.disabled = true; });
        existingPreviews.forEach(preview => preview.classList.add('d-none'));
    }
    refreshDropdown();
    }

    // 2. Ensure the toggle state is loaded correctly on page load
    document.addEventListener('DOMContentLoaded', function() {
        const useImages = document.getElementById('toggleImageOptions').checked;
        if (useImages) {
            toggleOptionMode();
        }
    });

       // 3. Refresh Dropdown (The missing link)
    function refreshDropdown() {
        const select = document.getElementById('correctAnswerSelect');
        if (!select) return;
        
        const isImageMode = document.getElementById('toggleImageOptions').checked;
        const textInputs = document.querySelectorAll('.option-text-input');
        const currentValue = select.value;
        
        select.innerHTML = '<option value="">-- Select Correct Option --</option>';
        
        textInputs.forEach((input, index) => {
            const text = input.value.trim();
            const letter = String.fromCharCode(65 + index);
            
            let display = text ? `${letter}. ${text}` : `${letter}. (Empty)`;
            if (isImageMode) display = `${letter}. (Image Option)`;
            
            const opt = document.createElement('option');
            // If image mode, the value needs to match the option index for the controller to match the image
             opt.value = index; 
            opt.textContent = display;
            
            if (opt.value === currentValue) opt.selected = true;
            select.appendChild(opt);
        });
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        refreshDropdown();
    });


</script>
@endpush