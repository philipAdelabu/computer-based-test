<!-- resources/views/admin/questions/create.blade.php -->
@extends('layouts.app')

@section('title', 'Add Question')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('page-title', 'Add New Question')

@section('content')
<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.questions.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }} ({{ $subject->class->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Difficulty Level</label>
                            <select name="difficulty" class="form-select @error('difficulty') is-invalid @enderror">
                                <option value="easy" {{ old('difficulty') == 'easy' ? 'selected' : '' }}>Easy</option>
                                <option value="medium" {{ old('difficulty') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="hard" {{ old('difficulty') == 'hard' ? 'selected' : '' }}>Hard</option>
                            </select>
                            @error('difficulty')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                        <!-- Add this right before the Question Text field -->
                         <br/>
                         <hr />
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Question Type <span class="text-danger">*</span></label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="question_type" id="typeObjective" value="objective" 
                                        {{ old('question_type', 'objective') == 'objective' ? 'checked' : '' }} onchange="toggleQuestionType()">
                                    <label class="form-check-label" for="typeObjective">
                                        <i class="bi bi-ui-checks text-primary"></i> Objective (Multiple Choice)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="question_type" id="typeTheory" value="theory" 
                                        {{ old('question_type') == 'theory' ? 'checked' : '' }} onchange="toggleQuestionType()">
                                    <label class="form-check-label" for="typeTheory">
                                        <i class="bi bi-pencil-square text-success"></i> Theory (Written Answer)
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr /><br/>
                     
                        <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Question <span class="text-danger">*</span>
                                </label>
                                <textarea name="question_text" 
                                        id="questionTextEditor" 
                                        class="form-control rich-editor @error('question_text') is-invalid @enderror"
                                        rows="6">{{ old('question_text') }}</textarea>
                                @error('question_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    <i class="bi bi-info-circle"></i>
                                    Use the toolbar to format text, add lists, tables, formulas, or insert images.
                                </small>
                            </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Question Image (Optional)</label>
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                            <small class="text-muted">Supported formats: JPG, PNG, GIF. Max size: 2MB</small>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div id="objectiveFields">
                            <!-- ============ OPTIONS SECTION ============ -->
                            <div class="mb-3" id="objectiveOptionsWrapper">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-semibold mb-0">Options <span class="text-danger">*</span></label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="toggleImageOptions" onchange="toggleOptionMode()">
                                        <label class="form-check-label small" for="toggleImageOptions">Use Images for Options</label>
                                    </div>
                                </div>
                                
                                <div id="options-container">
                                    @php
                                        $options = old('options', $question->options ?? ['', '', '', '']);
                                        if(!is_array($options)) $options = [];
                                        while (count($options) < 4) $options[] = '';
                                        $optionImages = old('option_images', $question->option_images ?? []);
                                    @endphp
                                    
                                    @foreach($options as $index => $option)
                                        <div class="input-group mb-2 option-row">
                                            <span class="input-group-text">{{ chr(65 + $index) }}</span>
                                            
                                            <!-- Text Input -->
                                            <input type="text" name="options[]" class="form-control option-text-input @error('options.*') is-invalid @enderror" 
                                                placeholder="Option {{ chr(65 + $index) }}" value="{{ $option }}" onkeyup="refreshDropdown()">
                                            
                                            <!-- Image Input -->
                                            <input type="file" name="option_images[{{ $index }}]" class="form-control option-image-input d-none" accept="image/*">
                                            
                                            @if(isset($optionImages[$index]))
                                                <span class="input-group-text bg-success text-white" title="Image uploaded">✓</span>
                                            @endif
                                            
                                            @if($index >= 4)
                                                <button type="button" class="btn btn-outline-danger remove-option"><i class="bi bi-x"></i></button>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-option">
                                    <i class="bi bi-plus-circle"></i> Add Option
                                </button>
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
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="bi bi-save"></i> Save Question
                        </button>
                        <a href="{{ route('admin.questions') }}" class="btn btn-secondary px-5">Cancel</a>
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
    let optionCount = 4;
    
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

<script>
    // 1. Toggle Theory vs Objective
    function toggleQuestionType() {
        const isTheory = document.getElementById('typeTheory').checked;
        const objOptions = document.getElementById('objectiveOptionsWrapper');
        const objCorrectAnswer = document.getElementById('objectiveCorrectAnswerWrapper');
        const theoryScore = document.getElementById('theoryScoreField'); // Your theory score div
        
        // Grab inputs inside the objective sections
        const objInputs = objOptions.querySelectorAll('input, select');
        const objCorrectInputs = objCorrectAnswer.querySelectorAll('input, select');

        if (isTheory) {
            // Hide Objective sections
            objOptions.style.display = 'none';
            objCorrectAnswer.style.display = 'none';
            
            // Disable Objective inputs so they don't submit or validate
            objInputs.forEach(input => input.disabled = true);
            objCorrectInputs.forEach(input => input.disabled = true);

            // Show Theory score
            if (theoryScore) {
                theoryScore.style.display = 'block';
                theoryScore.querySelector('input').disabled = false;
                theoryScore.querySelector('input').required = true;
            }
        } else {
            // Show Objective sections
            objOptions.style.display = 'block';
            objCorrectAnswer.style.display = 'block';
            
            // Enable Objective inputs
            objInputs.forEach(input => input.disabled = false);
            objCorrectInputs.forEach(input => {
                input.disabled = false;
                if(input.name === 'correct_answer') input.required = true; // Ensure dropdown is required
            });

            // Hide Theory score
            if (theoryScore) {
                theoryScore.style.display = 'none';
                theoryScore.querySelector('input').disabled = true;
                theoryScore.querySelector('input').required = false;
            }
        }
    }

    // 2. Toggle Text vs Image Options
    function toggleOptionMode() {
        const useImages = document.getElementById('toggleImageOptions').checked;
        const textInputs = document.querySelectorAll('.option-text-input');
        const imageInputs = document.querySelectorAll('.option-image-input');
        
        if (useImages) {
            textInputs.forEach(input => { input.classList.add('d-none'); input.disabled = true; });
            imageInputs.forEach(input => { input.classList.remove('d-none'); input.disabled = false; });
        } else {
            textInputs.forEach(input => { input.classList.remove('d-none'); input.disabled = false; });
            imageInputs.forEach(input => { input.classList.add('d-none'); imageInputs.disabled = true; });
        }
        refreshDropdown();
    }

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
        toggleQuestionType();
        refreshDropdown();
    });

    document.getElementById('examForm')?.addEventListener('submit', function(e) {
    const isTheory = document.getElementById('typeTheory').checked;
    const correctAnswer = document.getElementById('correctAnswerSelect').value;
    
    if (!isTheory && !correctAnswer) {
        e.preventDefault(); // Stop submission
        alert('Please select a correct answer for the objective question.');
        
        // Unhide the dropdown just in case
        document.getElementById('objectiveCorrectAnswerWrapper').style.display = 'block';
    }
});
</script>

@endpush

@include('partials.rich-editor')