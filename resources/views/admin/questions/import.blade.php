<!-- resources/views/admin/questions/import.blade.php -->
@extends('layouts.app')

@section('title', 'Import Questions')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('page-title', 'Import Questions from CSV')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.questions.import.csv') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Select Subject <span class="text-danger">*</span></label>
                        <select name="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                            <option value="">-- Select Subject --</option>
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
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">CSV File <span class="text-danger">*</span></label>
                        <div class="drop-zone border rounded p-4 text-center @error('file') border-danger @enderror" 
                             style="cursor: pointer; transition: all 0.3s; background: #f8f9fa;">
                            <input type="file" name="file" id="fileInput" class="d-none" accept=".csv,.txt" required>
                            <i class="bi bi-file-earmark-upload fs-1 text-primary"></i>
                            <h6 class="mt-2">Drop your CSV file here or click to browse</h6>
                            <small class="text-muted">Supported formats: .csv (Max: 5MB)</small>
                            <div id="fileInfo" class="mt-2" style="display: none;">
                                <div class="alert alert-info mb-0">
                                    <strong>Selected File:</strong>
                                    <span id="fileName"></span>
                                    <span id="fileSize" class="ms-2 text-muted"></span>
                                </div>
                            </div>
                        </div>
                        @error('file')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div id="loadingSpinner" style="display: none;" class="text-center my-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2">Importing questions... Please wait.</p>
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" style="width: 100%"></div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle"></i> CSV Format Instructions</h6>
                        <p class="mb-2">Your CSV file should have these columns in this exact order:</p>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Column</th>
                                        <th>Description</th>
                                        <th>Required</th>
                                        <th>Example</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><code>question</code></td>
                                        <td>The question text</td>
                                        <td><span class="badge bg-danger">Yes</span></td>
                                        <td>What is 2+2?</td>
                                    </tr>
                                    <tr>
                                        <td><code>option_a</code></td>
                                        <td>Option A</td>
                                        <td><span class="badge bg-danger">Yes</span></td>
                                        <td>3</td>
                                    </tr>
                                    <tr>
                                        <td><code>option_b</code></td>
                                        <td>Option B</td>
                                        <td><span class="badge bg-danger">Yes</span></td>
                                        <td>4</td>
                                    </tr>
                                    <tr>
                                        <td><code>option_c</code></td>
                                        <td>Option C</td>
                                        <td><span class="badge bg-danger">Yes</span></td>
                                        <td>5</td>
                                    </tr>
                                    <tr>
                                        <td><code>option_d</code></td>
                                        <td>Option D</td>
                                        <td><span class="badge bg-danger">Yes</span></td>
                                        <td>6</td>
                                    </tr>
                                    <tr>
                                        <td><code>correct_answer</code></td>
                                        <td>The correct answer</td>
                                        <td><span class="badge bg-danger">Yes</span></td>
                                        <td>4</td>
                                    </tr>
                                    <tr>
                                        <td><code>score</code></td>
                                        <td>Points (default: 1)</td>
                                        <td><span class="badge bg-secondary">No</span></td>
                                        <td>2</td>
                                    </tr>
                                    <tr>
                                        <td><code>difficulty</code></td>
                                        <td>easy, medium, or hard</td>
                                        <td><span class="badge bg-secondary">No</span></td>
                                        <td>easy</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="bi bi-download"></i> 
                                <a href="{{ route('admin.questions.download-template') }}" class="text-decoration-none">
                                    Download  Template
                                </a>
                            </small>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success btn-lg px-5" id="submitBtn">
                            <i class="bi bi-upload"></i> Import Questions
                        </button>
                        <a href="{{ route('admin.questions') }}" class="btn btn-secondary btn-lg px-5">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.drop-zone {
    cursor: pointer;
    transition: all 0.3s;
}
.drop-zone:hover {
    background: #e9ecef !important;
    border-color: #0d6efd !important;
}
.drop-zone.dragover {
    background: #e9ecef !important;
    border-color: #0d6efd !important;
    border-style: solid !important;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    const dropZone = document.querySelector('.drop-zone');
    const fileInput = document.getElementById('fileInput');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');

    // Click on drop zone to open file dialog
    dropZone.addEventListener('click', function() {
        fileInput.click();
    });

    // File selected
    fileInput.addEventListener('change', function() {
        handleFile(this.files[0]);
    });

    // Drag and drop
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFile(files[0]);
        }
    });

    // Handle file
    function handleFile(file) {
        if (!file) {
            fileInfo.style.display = 'none';
            return;
        }

        // Validate file type
        const validTypes = ['text/csv', 'application/vnd.ms-excel'];
        if (!validTypes.includes(file.type) && !file.name.endsWith('.csv')) {
            alert('Please upload a valid CSV file.');
            fileInput.value = '';
            fileInfo.style.display = 'none';
            return;
        }

        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB.');
            fileInput.value = '';
            fileInfo.style.display = 'none';
            return;
        }

        // Show file info
        const size = (file.size / 1024).toFixed(2);
        fileName.textContent = file.name;
        fileSize.textContent = `(${size} KB)`;
        fileInfo.style.display = 'block';
    }

    // Show loading spinner on form submit
    $('#importForm').on('submit', function(e) {
        const file = fileInput.files[0];
        if (!file) {
            e.preventDefault();
            alert('Please select a CSV file to import.');
            return;
        }

        $('#loadingSpinner').show();
        $('#submitBtn').prop('disabled', true);
        $('#submitBtn').html('<span class="spinner-border spinner-border-sm" role="status"></span> Importing...');
    });

    // Reset form
    function resetForm() {
        fileInput.value = '';
        fileInfo.style.display = 'none';
        $('#loadingSpinner').hide();
        $('#submitBtn').prop('disabled', false);
        $('#submitBtn').html('<i class="bi bi-upload"></i> Import Questions');
    }
});
</script>
@endpush