{{--
    Reusable Photo Upload + Crop Component

    Usage:
    @include('partials.photo-upload-cropper', [
        'name' => 'photo',                    // form field name
        'currentPhoto' => $user->photo_url ?? null,  // existing photo (for edit)
        'removeName' => 'remove_photo',       // hidden input for removal flag
        'userId' => 'student',                // unique ID prefix
        'aspectRatio' => 1,                   // 1 = square, 4/3 = landscape
        'maxSize' => 2,                       // max file size in MB
        'helpText' => 'JPG, PNG, or WEBP. Max 2MB.',
    ])
--}}

@php
    $name = $name ?? 'photo';
    $currentPhoto = $currentPhoto ?? null;
    $removeName = $removeName ?? 'remove_photo';
    $userId = $userId ?? 'user';
    $aspectRatio = $aspectRatio ?? 1;
    $maxSize = $maxSize ?? 2;
    $helpText = $helpText ?? 'JPG, PNG, or WEBP. Max ' . $maxSize . 'MB.';
@endphp

<div class="photo-crop-component" id="{{ $userId }}-component">
    <!-- Preview Circle -->
    <div class="text-center">
        <div class="photo-crop-preview" id="{{ $userId }}-preview">
            @if($currentPhoto)
                <img src="{{ $currentPhoto }}" alt="Current Photo" id="{{ $userId }}-previewImg">
            @else
                <div class="photo-crop-placeholder" id="{{ $userId }}-placeholder">
                    <i class="bi bi-camera"></i>
                    <span>Add Photo</span>
                </div>
                <img src="" alt="" id="{{ $userId }}-previewImg" style="display: none;">
            @endif
        </div>

        <!-- Hidden file input -->
        <input type="file" 
               name="{{ $name }}" 
               id="{{ $userId }}-fileInput" 
               class="d-none" 
               accept="image/jpeg,image/png,image/jpg,image/webp">

        <!-- Hidden remove flag -->
        <input type="hidden" name="{{ $removeName }}" id="{{ $userId }}-removeFlag" value="0">

        <!-- Buttons -->
        <div class="mt-2 d-flex justify-content-center gap-2 flex-wrap">
            <button type="button" 
                    class="btn btn-sm btn-outline-primary" 
                    onclick="document.getElementById('{{ $userId }}-fileInput').click()">
                <i class="bi bi-upload"></i> 
                {{ $currentPhoto ? 'Change Photo' : 'Choose Photo' }}
            </button>
            <button type="button" 
                    class="btn btn-sm btn-outline-danger {{ $currentPhoto ? '' : 'd-none' }}" 
                    id="{{ $userId }}-removeBtn" 
                    onclick="removePhoto_{{ $userId }}()">
                <i class="bi bi-x-circle"></i> Remove
            </button>
        </div>

        <div class="form-text mt-1">{{ $helpText }}</div>
    </div>
</div>

<!-- CROP MODAL -->
<div class="modal fade" id="{{ $userId }}-cropModal" tabindex="-1" 
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-crop text-primary"></i> 
                    Crop Photo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" 
                        onclick="cancelCrop_{{ $userId }}()"></button>
            </div>
            <div class="modal-body p-0">
                <div class="crop-container">
                    <img id="{{ $userId }}-cropImage" src="" alt="Crop" 
                         style="max-width: 100%; display: block;">
                </div>
            </div>
            <div class="modal-footer flex-column">
                <!-- Cropper controls -->
                <div class="crop-controls w-100 mb-2">
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                onclick="rotateCrop_{{ $userId }}(-90)" title="Rotate Left">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                onclick="rotateCrop_{{ $userId }}(90)" title="Rotate Right">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                onclick="flipCrop_{{ $userId }}('h')" title="Flip Horizontal">
                            <i class="bi bi-arrow-left-right"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                onclick="flipCrop_{{ $userId }}('v')" title="Flip Vertical">
                            <i class="bi bi-arrow-down-up"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                onclick="resetCrop_{{ $userId }}()" title="Reset">
                            <i class="bi bi-arrow-repeat"></i> Reset
                        </button>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-secondary" 
                                    onclick="setZoom_{{ $userId }}(0.1)" title="Zoom In">
                                <i class="bi bi-zoom-in"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" 
                                    onclick="setZoom_{{ $userId }}(-0.1)" title="Zoom Out">
                                <i class="bi bi-zoom-out"></i>
                            </button>
                        </div>
                    </div>
                    <div class="text-center mt-2">
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> 
                            Drag to position. Use the handles to resize the crop area.
                        </small>
                    </div>
                </div>
                
                <div class="d-flex gap-2 w-100">
                    <button type="button" class="btn btn-secondary flex-fill" 
                            data-bs-dismiss="modal" onclick="cancelCrop_{{ $userId }}()">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary flex-fill" 
                            onclick="applyCrop_{{ $userId }}()">
                        <i class="bi bi-check-circle"></i> Apply Crop
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@once
@push('styles')
<style>
/* ============ PHOTO CROP COMPONENT ============ */
.photo-crop-preview {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    border: 3px dashed #cbd5e0;
    background: #f8f9fc;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    transition: all 0.3s;
    position: relative;
}
.photo-crop-preview:hover {
    border-color: #4e73df;
    background: #f0f4ff;
}
.photo-crop-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.photo-crop-placeholder {
    text-align: center;
    color: #999;
    pointer-events: none;
}
.photo-crop-placeholder i {
    font-size: 2rem;
    display: block;
    margin-bottom: 0.25rem;
}
.photo-crop-placeholder span {
    font-size: 0.75rem;
}

/* ============ CROP MODAL ============ */
.crop-container {
    background: #1a1a1a;
    max-height: 60vh;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border-radius: 0;
}
#{{ $userId ?? 'default' }}-cropImage,
.crop-container img {
    max-width: 100%;
    max-height: 60vh;
    display: block;
}

/* Cropper.js customization */
.cropper-view-box,
.cropper-face {
    border-radius: 50%;
}
.cropper-view-box {
    outline: 2px solid #4e73df;
    outline-color: rgba(78, 115, 223, 0.75);
}
.cropper-line, .cropper-point {
    background-color: #4e73df;
}
.cropper-point {
    width: 10px;
    height: 10px;
}

.crop-controls {
    border-bottom: 1px solid #e3e6f0;
    padding-bottom: 0.75rem;
}

/* Mobile */
@media (max-width: 767.98px) {
    .photo-crop-preview {
        width: 120px;
        height: 120px;
    }
    .crop-container {
        max-height: 50vh;
    }
    .crop-container img {
        max-height: 50vh;
    }
    .crop-controls .btn {
        min-width: 40px;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Namespaced cropper instance (safe for multiple components on one page)
window.CropperInstances = window.CropperInstances || {};

// Wait until DOM is ready
(function() {
    const userId = '{{ $userId }}';
    const aspectRatio = {{ $aspectRatio }};
    const maxSizeMB = {{ $maxSize }};
    const maxSizeBytes = maxSizeMB * 1024 * 1024;

    const componentId = userId + '-component';
    const fileInput = document.getElementById(userId + '-fileInput');
    const previewImg = document.getElementById(userId + '-previewImg');
    const placeholder = document.getElementById(userId + '-placeholder');
    const removeBtn = document.getElementById(userId + '-removeBtn');
    const removeFlag = document.getElementById(userId + '-removeFlag');
    const cropModal = document.getElementById(userId + '-cropModal');
    const cropImage = document.getElementById(userId + '-cropImage');

    let cropper = null;
    let bsModal = null;

    // Init modal
    if (cropModal) {
        bsModal = new bootstrap.Modal(cropModal);
    }

    // File input change → open crop modal
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Validate size
            if (file.size > maxSizeBytes) {
                alert('File is too large. Maximum size is ' + maxSizeMB + 'MB.');
                fileInput.value = '';
                return;
            }

            // Validate type
            if (!file.type.startsWith('image/')) {
                alert('Please select an image file.');
                fileInput.value = '';
                return;
            }

            // Load image into crop modal
            const reader = new FileReader();
            reader.onload = function(event) {
                cropImage.src = event.target.result;
                
                // Show modal
                bsModal.show();
                
                // Initialize cropper once the image is loaded
                cropImage.onload = function() {
                    if (cropper) {
                        cropper.destroy();
                    }
                    
                    cropper = new Cropper(cropImage, {
                        aspectRatio: aspectRatio,
                        viewMode: 1,
                        dragMode: 'move',
                        autoCropArea: 0.9,
                        restore: false,
                        guides: false,
                        center: true,
                        highlight: false,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: false,
                        responsive: true,
                        background: true,
                        checkOrientation: true,
                        modal: true,
                    });
                    
                    window.CropperInstances[userId] = cropper;
                };
            };
            reader.readAsDataURL(file);
        });
    }

    // Rotate
    window['rotateCrop_' + userId] = function(deg) {
        if (cropper) cropper.rotate(deg);
    };

    // Flip
    window['flipCrop_' + userId] = function(direction) {
        if (!cropper) return;
        const scaleX = cropper.getData().scaleX || 1;
        const scaleY = cropper.getData().scaleY || 1;
        
        if (direction === 'h') {
            cropper.scaleX(-scaleX);
        } else {
            cropper.scaleY(-scaleY);
        }
    };

    // Reset
    window['resetCrop_' + userId] = function() {
        if (cropper) cropper.reset();
    };

    // Zoom
    window['setZoom_' + userId] = function(ratio) {
        if (cropper) cropper.zoom(ratio);
    };

    // Cancel
    window['cancelCrop_' + userId] = function() {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        fileInput.value = '';
        cropImage.src = '';
    };

    // Apply crop
    window['applyCrop_' + userId] = function() {
        if (!cropper) return;

        // Get cropped canvas
        const canvas = cropper.getCroppedCanvas({
            width: 500,
            height: 500,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        // Convert to blob and replace the file input
        canvas.toBlob(function(blob) {
            // Create a new File object
            const fileName = 'cropped_' + Date.now() + '.jpg';
            const croppedFile = new File([blob], fileName, {
                type: 'image/jpeg',
                lastModified: Date.now(),
            });

            // Create DataTransfer to update the file input
            const dt = new DataTransfer();
            dt.items.add(croppedFile);
            fileInput.files = dt.files;

            // Update preview
            const previewUrl = URL.createObjectURL(blob);
            previewImg.src = previewUrl;
            previewImg.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';
            if (removeBtn) removeBtn.classList.remove('d-none');
            
            // Reset remove flag
            if (removeFlag) removeFlag.value = '0';

            // Cleanup
            cropper.destroy();
            cropper = null;
            bsModal.hide();
            cropImage.src = '';
        }, 'image/jpeg', 0.92);
    };

    // Remove photo
    window['removePhoto_' + userId] = function() {
        if (!confirm('Remove this photo?')) return;

        fileInput.value = '';
        removeFlag.value = '1';
        
        previewImg.src = '';
        previewImg.style.display = 'none';
        
        if (placeholder) {
            placeholder.style.display = 'block';
        } else {
            // Recreate placeholder
            const preview = document.getElementById(userId + '-preview');
            const ph = document.createElement('div');
            ph.className = 'photo-crop-placeholder';
            ph.id = userId + '-placeholder';
            ph.innerHTML = '<i class="bi bi-camera"></i><span>Add Photo</span>';
            preview.insertBefore(ph, previewImg);
        }
        
        removeBtn.classList.add('d-none');
    };
})();
</script>
@endpush
@endonce