<!-- resources/views/admin/classes/admit.blade.php -->
@extends('layouts.app')

@section('title', 'Admit Students')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('page-title', 'Admit Students to Next Class')

@section('content')
<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            <strong>Instructions:</strong>
            <ul class="mb-0 mt-2">
                <li>Select a class to view all active students</li>
                <li>Choose the students you want to admit</li>
                <li>Select the next class to move them to</li>
                <li>Students will be moved to the new class while maintaining their active status</li>
            </ul>
        </div>

        <div class="card">
            <div class="card-body">
                <form id="admissionForm" action="{{ route('admin.classes.admit.process') }}" method="POST">
                    @csrf
                    
                    <!-- Step 1: Select Current Class -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Step 1: Select Current Class <span class="text-danger">*</span></label>
                        <select id="currentClassSelect" name="current_class_id" class="form-select @error('current_class_id') is-invalid @enderror" required>
                            <option value="">-- Select Current Class --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('current_class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }} ({{ $class->students->where('status', 'active')->count() }} active students)
                                </option>
                            @endforeach
                        </select>
                        @error('current_class_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Step 2: Select Students -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Step 2: Select Students to Admit <span class="text-danger">*</span></label>
                        <div id="studentsContainer" class="border rounded p-3" style="min-height: 150px;">
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-people fs-1 d-block"></i>
                                <p>Select a class above to load students</p>
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllStudents()">
                                <i class="bi bi-check-all"></i> Select All
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllStudents()">
                                <i class="bi bi-x-circle"></i> Deselect All
                            </button>
                            <span id="selectedCount" class="ms-3 text-muted">Selected: 0 students</span>
                        </div>
                        @error('student_ids')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Step 3: Select Next Class -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Step 3: Select Next Class <span class="text-danger">*</span></label>
                        <select name="next_class_id" class="form-select @error('next_class_id') is-invalid @enderror" required>
                            <option value="">-- Select Next Class --</option>
                            @foreach($allClasses as $class)
                                <option value="{{ $class->id }}" {{ old('next_class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('next_class_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Select the class where students should be moved to</small>
                    </div>

                    <!-- Summary -->
                    <div id="summarySection" class="alert alert-info" style="display: none;">
                        <h6><i class="bi bi-file-text"></i> Summary</h6>
                        <p class="mb-0">
                            You are about to admit <strong id="summaryCount">0</strong> student(s) from 
                            <strong id="summaryCurrentClass">-</strong> to <strong id="summaryNextClass">-</strong>
                        </p>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success btn-lg px-5" id="submitBtn" disabled>
                            <i class="bi bi-arrow-up-circle"></i> Admit Selected Students
                        </button>
                        <a href="{{ route('admin.classes') }}" class="btn btn-secondary btn-lg px-5">Cancel</a>
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
    let selectedStudents = [];
    
    // Debug: Check if the route is accessible
    console.log('Admission page loaded');
    
    // Load students when class is selected
    $('#currentClassSelect').on('change', function() {
        const classId = $(this).val();
        console.log('Class selected:', classId);
        
        if (!classId) {
            $('#studentsContainer').html(`
                <div class="text-center text-muted py-4">
                    <i class="bi bi-people fs-1 d-block"></i>
                    <p>Select a class above to load students</p>
                </div>
            `);
            updateSummary();
            return;
        }
        
        // Show loading
        $('#studentsContainer').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted mt-2">Loading students...</p>
            </div>
        `);
        const baseUrl = "{{ url('/') }}";
        // Fetch students with proper error handling
        const url = `${baseUrl}/admin/students/by-class/${classId}`;
        console.log('Fetching students from:', url);
        
        $.ajax({
            url: url,
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('Students loaded:', response);
                
                if (!response || response.length === 0) {
                    $('#studentsContainer').html(`
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-people fs-1 d-block"></i>
                            <p>No active students found in this class</p>
                        </div>
                    `);
                    return;
                }
                
                let html = `<div class="row">`;
                response.forEach((student, index) => {
                    html += `
                        <div class="col-md-6 col-lg-4">
                            <div class="form-check p-3 border rounded mb-2 student-item" 
                                 style="cursor: pointer; transition: all 0.2s;">
                                <input class="form-check-input student-checkbox" 
                                       type="checkbox" 
                                       name="student_ids[]" 
                                       value="${student.id}" 
                                       id="student_${student.id}">
                                <label class="form-check-label w-100" for="student_${student.id}">
                                    <strong>${student.name || 'Unknown'}</strong>
                                    <br>
                                    <small class="text-muted">Admission: ${student.admission_number || 'N/A'}</small>
                                    <br>
                                    <small class="text-muted">Email: ${student.email || 'N/A'}</small>
                                </label>
                            </div>
                        </div>
                    `;
                });
                html += `</div>`;
                $('#studentsContainer').html(html);
                
                // Add click event to the entire card
                $('.student-item').on('click', function(e) {
                    // Prevent if clicking on the checkbox itself (to avoid double toggle)
                    if ($(e.target).is('input')) {
                        return;
                    }
                    const checkbox = $(this).find('.student-checkbox');
                    checkbox.prop('checked', !checkbox.prop('checked'));
                    updateSelectedCount();
                });
                
                // Update count on checkbox change
                $('.student-checkbox').on('change', function() {
                    updateSelectedCount();
                });
                
                updateSelectedCount();
            },
            error: function(xhr, status, error) {
                console.error('Error loading students:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                
                let errorMessage = 'Error loading students. Please try again.';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.error) {
                        errorMessage = response.error;
                    }
                } catch (e) {
                    // If response is not JSON, use default message
                }
                
                $('#studentsContainer').html(`
                    <div class="text-center text-danger py-4">
                        <i class="bi bi-exclamation-triangle fs-1 d-block"></i>
                        <p>${errorMessage}</p>
                        <button class="btn btn-sm btn-outline-primary mt-2" onclick="location.reload()">
                            <i class="bi bi-arrow-clockwise"></i> Refresh Page
                        </button>
                    </div>
                `);
            }
        });
    });
    
    // Update selected count
    function updateSelectedCount() {
        const checked = $('.student-checkbox:checked');
        const count = checked.length;
        $('#selectedCount').text(`Selected: ${count} student(s)`);
        updateSummary();
        
        // Enable/disable submit button
        if (count > 0 && $('#currentClassSelect').val() && $('select[name="next_class_id"]').val()) {
            $('#submitBtn').prop('disabled', false);
        } else {
            $('#submitBtn').prop('disabled', true);
        }
    }
    
    // Update summary
    function updateSummary() {
        const count = $('.student-checkbox:checked').length;
        const currentClass = $('#currentClassSelect option:selected').text();
        const nextClass = $('select[name="next_class_id"] option:selected').text();
        
        if (count > 0 && currentClass && nextClass && currentClass !== '-- Select Current Class --' && nextClass !== '-- Select Next Class --') {
            $('#summarySection').show();
            $('#summaryCount').text(count);
            $('#summaryCurrentClass').text(currentClass.split('(')[0].trim());
            $('#summaryNextClass').text(nextClass);
        } else {
            $('#summarySection').hide();
        }
    }
    
    // Select all students
    window.selectAllStudents = function() {
        $('.student-checkbox').prop('checked', true);
        updateSelectedCount();
    };
    
    // Deselect all students
    window.deselectAllStudents = function() {
        $('.student-checkbox').prop('checked', false);
        updateSelectedCount();
    };
    
    // Update summary when next class changes
    $('select[name="next_class_id"]').on('change', function() {
        updateSelectedCount();
    });
    
    // Prevent form submission if no students selected
    $('#admissionForm').on('submit', function(e) {
        const count = $('.student-checkbox:checked').length;
        if (count === 0) {
            e.preventDefault();
            alert('Please select at least one student to admit.');
            return false;
        }
        
        const currentClass = $('#currentClassSelect').val();
        const nextClass = $('select[name="next_class_id"]').val();
        
        if (currentClass === nextClass) {
            e.preventDefault();
            alert('Current class and next class cannot be the same. Please select a different class.');
            return false;
        }
        
        return confirm(`Are you sure you want to admit ${count} student(s) to the next class? This action cannot be undone.`);
    });
});
</script>
@endpush