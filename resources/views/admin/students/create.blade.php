<!-- resources/views/admin/students/create.blade.php -->
@extends('layouts.app')

@section('title', 'Add Student')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('page-title', 'Add New Student')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.students.store') }}" method="POST"  enctype="multipart/form-data">
                    @csrf

                       <!-- ============ PHOTO UPLOAD ============ -->
                    <div class="row mb-4">
                        <div class="col-12 text-center">
                            <div class="photo-upload-wrapper">
                                @include('partials.photo-upload-cropper', [
                                    'name' => 'photo',
                                    'currentPhoto' => null,
                                    'userId' => 'student-create',
                                    'aspectRatio' => 1,
                                    'maxSize' => 2,
                                    'helpText' => 'JPG, PNG, or WEBP. Max 2MB. The image will be cropped to a square.',
                                ])
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Admission Number <span class="text-danger">*</span></label>
                            <input type="text" name="admission_number" class="form-control @error('admission_number') is-invalid @enderror" 
                                   value="{{ old('admission_number') }}" required>
                            @error('admission_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Class <span class="text-danger">*</span></label>
                            <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                   value="{{ old('date_of_birth') }}">
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Guardian Name</label>
                            <input type="text" name="guardian_name" class="form-control @error('guardian_name') is-invalid @enderror" 
                                   value="{{ old('guardian_name') }}">
                            @error('guardian_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Guardian Phone</label>
                            <input type="text" name="guardian_phone" class="form-control @error('guardian_phone') is-invalid @enderror" 
                                   value="{{ old('guardian_phone') }}">
                            @error('guardian_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="bi bi-save"></i> Save Student
                        </button>
                        <a href="{{ route('admin.students') }}" class="btn btn-secondary px-5">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


@push('styles')
<style>
.photo-upload-wrapper {
    display: inline-block;
}
.photo-preview {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    border: 3px dashed #cbd5e0;
    background: #f8f9fc;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    margin: 0 auto;
    transition: all 0.3s;
}
.photo-preview:hover {
    border-color: #4e73df;
    background: #f0f4ff;
}
.photo-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.photo-placeholder {
    text-align: center;
    color: #999;
}
.photo-placeholder i {
    font-size: 2rem;
    display: block;
    margin-bottom: 0.25rem;
}
.photo-placeholder span {
    font-size: 0.75rem;
}
</style>
@endpush

@push('scripts')
<script>
document.getElementById('photoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('photoPreview');
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
            document.getElementById('removePhotoBtn').classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }
});

function removePhoto() {
    document.getElementById('photoInput').value = '';
    document.getElementById('photoPreview').innerHTML = `
        <div class="photo-placeholder">
            <i class="bi bi-camera"></i>
            <span>Upload Photo</span>
        </div>
    `;
    document.getElementById('removePhotoBtn').classList.add('d-none');
}
</script>
@endpush