<!-- resources/views/admin/students/edit.blade.php -->
@extends('layouts.app')

@section('title', 'Edit Student')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('page-title', 'Edit Student')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.students.update', $student->id) }}" method="POST"  enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                       <!-- ============ PHOTO UPLOAD ============ -->
                    <div class="row mb-4">
                        <div class="col-12 text-center">
                               @include('partials.photo-upload-cropper', [
                                    'name' => 'photo',
                                    'currentPhoto' => $student->user->photo_url,
                                    'userId' => 'student-edit',
                                    'aspectRatio' => 1,
                                    'maxSize' => 2,
                                    'helpText' => 'JPG, PNG, or WEBP. Max 2MB. The image will be cropped to a square.',
                                ])
                        </div>
                    </div>
                    
                    <!-- Hidden field to signal photo removal -->
                    <input type="hidden" name="remove_photo" id="removePhotoFlag" value="0">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $student->user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $student->user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password <small class="text-muted">(Leave blank to keep current)</small></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Admission Number</label>
                            <input type="text" class="form-control" value="{{ $student->admission_number }}" disabled>
                            <small class="text-muted">Admission number cannot be changed</small>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Class <span class="text-danger">*</span></label>
                            <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id', $student->class_id) == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="active" {{ old('status', $student->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="graduated" {{ old('status', $student->status) == 'graduated' ? 'selected' : '' }}>Graduated</option>
                                <option value="suspended" {{ old('status', $student->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                   value="{{ old('date_of_birth', $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : '') }}">
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                          <div class="col-md-4">
                            <label class="form-label fw-semibold">Gender</label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                <option value="">-- Select Gender --</option>
                                <option value="Male" {{ old('gender', $student->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender', $student->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Guardian Name</label>
                            <input type="text" name="guardian_name" class="form-control @error('guardian_name') is-invalid @enderror" 
                                   value="{{ old('guardian_name', $student->guardian_name) }}">
                            @error('guardian_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Guardian Phone</label>
                            <input type="text" name="guardian_phone" class="form-control @error('guardian_phone') is-invalid @enderror" 
                                   value="{{ old('guardian_phone', $student->guardian_phone) }}">
                            @error('guardian_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="bi bi-save"></i> Update Student
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
            document.getElementById('removePhotoBtn')?.classList.remove('d-none');
            document.getElementById('removePhotoFlag').value = '0';
        };
        reader.readAsDataURL(file);
    }
});

function removePhoto() {
    document.getElementById('photoInput').value = '';
    document.getElementById('removePhotoFlag').value = '1';
    document.getElementById('photoPreview').innerHTML = `
        <div class="photo-placeholder">
            <i class="bi bi-camera"></i>
            <span>Upload Photo</span>
        </div>
    `;
    document.getElementById('removePhotoBtn')?.classList.add('d-none');
}
</script>
@endpush