<!-- resources/views/admin/settings/index.blade.php -->
@extends('layouts.app')

@section('title', 'System Settings')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('page-title', 'System Settings')

@section('content')
<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row">
        <!-- LEFT COLUMN: School Identity -->
        <div class="col-lg-7">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="bi bi-building text-primary"></i> School Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            School Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="school_name" 
                               class="form-control @error('school_name') is-invalid @enderror" 
                               value="{{ old('school_name', \App\Helpers\SettingHelper::schoolName()) }}" 
                               required>
                        <small class="text-muted">This will appear on the landing page, login page, and dashboard</small>
                        @error('school_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">School Motto / Slogan</label>
                        <input type="text" name="school_motto" class="form-control" 
                               value="{{ old('school_motto', \App\Helpers\SettingHelper::schoolMotto()) }}">
                        <small class="text-muted">Short tagline for your school</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="text" name="school_phone" class="form-control" 
                                   value="{{ old('school_phone', \App\Helpers\SettingHelper::schoolPhone()) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="school_email" class="form-control" 
                                   value="{{ old('school_email', \App\Helpers\SettingHelper::schoolEmail()) }}">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Address</label>
                        <textarea name="school_address" class="form-control" rows="2">{{ old('school_address', \App\Helpers\SettingHelper::schoolAddress()) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Landing Page Settings -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="bi bi-layout-text-window text-primary"></i> Landing Page
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Landing Page Title</label>
                        <input type="text" name="landing_page_title" class="form-control" 
                               value="{{ old('landing_page_title', \App\Models\Setting::get('landing_page_title', 'Welcome to Our CBT System')) }}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Landing Page Description</label>
                        <textarea name="landing_page_description" class="form-control" rows="3">{{ old('landing_page_description', \App\Models\Setting::get('landing_page_description', 'A modern computer-based testing platform for schools and institutions.')) }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Footer Text</label>
                        <input type="text" name="footer_text" class="form-control" 
                               value="{{ old('footer_text', \App\Models\Setting::get('footer_text', '© ' . date('Y') . ' CBT System')) }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Logo & Preview -->
        <div class="col-lg-5">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="bi bi-image text-primary"></i> School Logo
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Current Logo -->
                    <div class="text-center mb-3">
                        <div class="logo-preview-box">
                            @php
                                $logoUrl = \App\Helpers\SettingHelper::schoolLogo();
                            @endphp
                            @if($logoUrl)
                                <img src="{{ $logoUrl }}" 
                                     alt="School Logo" 
                                     id="logoPreview"
                                     class="logo-preview-img">
                            @else
                                <div id="logoPreviewPlaceholder" class="logo-placeholder">
                                    <i class="bi bi-image fs-1 text-muted"></i>
                                    <p class="text-muted mb-0 small">No logo uploaded</p>
                                </div>
                                <img src="" alt="" id="logoPreview" class="logo-preview-img" style="display: none;">
                            @endif
                        </div>
                    </div>
                    
                    <!-- Upload Input -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Upload New Logo</label>
                        <input type="file" name="school_logo" id="logoInput" 
                               class="form-control @error('school_logo') is-invalid @enderror" 
                               accept="image/png,image/jpeg,image/jpg,image/gif,image/svg+xml,image/webp">
                        <small class="text-muted d-block mt-1">
                            <i class="bi bi-info-circle"></i> 
                            PNG, JPG, SVG, or WEBP. Max 2MB. Recommended: 200x200px
                        </small>
                        @error('school_logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    @if($logoUrl)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remove_logo" value="1" id="removeLogo">
                            <label class="form-check-label text-danger" for="removeLogo">
                                <i class="bi bi-trash"></i> Remove current logo
                            </label>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Live Preview -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="bi bi-eye text-primary"></i> Preview
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">This is how it will appear on the login page:</p>
                    <div class="preview-container">
                        <div class="preview-logo">
                            <img src="{{ $logoUrl ?: 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2260%22 height=%2260%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%23667eea%22 stroke-width=%222%22%3E%3Crect x=%222%22 y=%223%22 width=%2220%22 height=%2214%22 rx=%222%22/%3E%3Cpath d=%22M8 21h8M12 17v4%22/%3E%3C/svg%3E' }}" 
                                 alt="Logo" 
                                 class="preview-img" 
                                 id="previewImg">
                        </div>
                        <div class="preview-school-name" id="previewSchoolName">
                            {{ \App\Helpers\SettingHelper::schoolName() }}
                        </div>
                        <div class="preview-motto" id="previewMotto">
                            {{ \App\Helpers\SettingHelper::schoolMotto() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Buttons -->
    <div class="card">
        <div class="card-body text-center">
            <button type="submit" class="btn btn-primary px-5">
                <i class="bi bi-save"></i> Save Settings
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary px-5">
                Cancel
            </a>
        </div>
    </div>
</form>
@endsection

@push('styles')
<style>
.logo-preview-box {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 150px;
    height: 150px;
    border: 2px dashed #dee2e6;
    border-radius: 10px;
    background: #f8f9fa;
    padding: 10px;
}
.logo-preview-img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}
.logo-placeholder {
    text-align: center;
}
.preview-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    color: white;
}
.preview-logo {
    margin-bottom: 10px;
}
.preview-img {
    max-width: 60px;
    max-height: 60px;
    object-fit: contain;
    background: white;
    border-radius: 10px;
    padding: 5px;
}
.preview-school-name {
    font-size: 18px;
    font-weight: 700;
    color: white;
}
.preview-motto {
    font-size: 11px;
    color: rgba(255,255,255,0.85);
    font-style: italic;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Live logo preview
    $('#logoInput').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#logoPreview').attr('src', e.target.result).show();
                $('#logoPreviewPlaceholder').hide();
                $('#previewImg').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Live school name preview
    $('input[name="school_name"]').on('input', function() {
        $('#previewSchoolName').text($(this).val() || 'School Name');
    });
    
    // Live motto preview
    $('input[name="school_motto"]').on('input', function() {
        $('#previewMotto').text($(this).val() || 'School Motto');
    });
});
</script>
@endpush