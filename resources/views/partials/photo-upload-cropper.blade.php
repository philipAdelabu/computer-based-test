{{--
    Reusable Photo Upload + Crop Component
--}}

@php
    $name = $name ?? 'photo';
    $currentPhoto = $currentPhoto ?? null;
    $removeName = $removeName ?? 'remove_photo';
    $userId = $userId ?? 'user-' . uniqid();   // ← guarantee uniqueness
    $aspectRatio = $aspectRatio ?? 1;
    $maxSize = $maxSize ?? 2;
    $helpText = $helpText ?? 'JPG, PNG, or WEBP. Max ' . $maxSize . 'MB. The image will be cropped to a square.';
@endphp

<div class="photo-crop-component" 
     id="{{ $userId }}-component"
     data-photo-cropper
     data-user-id="{{ $userId }}"
     data-aspect-ratio="{{ $aspectRatio }}"
     data-max-size="{{ $maxSize }}">
    
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
                    onclick="removePhoto('{{ $userId }}')">
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
                <button type="button" class="btn-close" 
                        data-bs-dismiss="modal" 
                        onclick="cancelCrop('{{ $userId }}')"></button>
            </div>
            <div class="modal-body p-0">
                <div class="crop-container">
                    <img id="{{ $userId }}-cropImage" src="" alt="Crop" 
                         style="max-width: 100%; display: block;">
                </div>
            </div>
            <div class="modal-footer flex-column">
                <div class="crop-controls w-100 mb-2">
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                onclick="rotateCrop('{{ $userId }}', -90)" title="Rotate Left">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                onclick="rotateCrop('{{ $userId }}', 90)" title="Rotate Right">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                onclick="flipCrop('{{ $userId }}', 'h')" title="Flip Horizontal">
                            <i class="bi bi-arrow-left-right"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                onclick="flipCrop('{{ $userId }}', 'v')" title="Flip Vertical">
                            <i class="bi bi-arrow-down-up"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                onclick="resetCrop('{{ $userId }}')" title="Reset">
                            <i class="bi bi-arrow-repeat"></i> Reset
                        </button>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-secondary" 
                                    onclick="setZoom('{{ $userId }}', 0.1)" title="Zoom In">
                                <i class="bi bi-zoom-in"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" 
                                    onclick="setZoom('{{ $userId }}', -0.1)" title="Zoom Out">
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
                            data-bs-dismiss="modal" 
                            onclick="cancelCrop('{{ $userId }}')">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary flex-fill" 
                            onclick="applyCrop('{{ $userId }}')">
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
@endonce

@once
@push('scripts')
<script>
// Global cropper registry
window.CropperRegistry = window.CropperRegistry || {};

/**
 * Initialize a crop component for a specific user ID
 */
function initPhotoCropper(userId, aspectRatio, maxSizeMB) {
    // Prevent double-init
    if (window.CropperRegistry[userId]) return;
    
    const maxSizeBytes = maxSizeMB * 1024 * 1024;
    
    const fileInput = document.getElementById(userId + '-fileInput');
    const previewImg = document.getElementById(userId + '-previewImg');
    const placeholder = document.getElementById(userId + '-placeholder');
    const removeBtn = document.getElementById(userId + '-removeBtn');
    const removeFlag = document.getElementById(userId + '-removeFlag');
    const cropModalEl = document.getElementById(userId + '-cropModal');
    const cropImage = document.getElementById(userId + '-cropImage');
    
    if (!fileInput || !cropModalEl || !cropImage) {
        console.warn('Crop component elements not found for:', userId);
        return;
    }
    
    let cropper = null;
    const bsModal = new bootstrap.Modal(cropModalEl);
    
    // Store instance
    window.CropperRegistry[userId] = {
        getCropper: () => cropper,
        setCropper: (c) => { cropper = c; },
        bsModal: bsModal,
        fileInput: fileInput,
        previewImg: previewImg,
        placeholder: placeholder,
        removeBtn: removeBtn,
        removeFlag: removeFlag,
        cropImage: cropImage,
    };
    
    // File input change → open crop modal
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
            bsModal.show();
            
            cropImage.onload = function() {
                if (cropper) cropper.destroy();
                
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
                
                window.CropperRegistry[userId].setCropper(cropper);
            };
        };
        reader.readAsDataURL(file);
    });
}

/* ============================================ */
/* GLOBAL FUNCTIONS (called from onclick)      */
/* ============================================ */

function rotateCrop(userId, deg) {
    const reg = window.CropperRegistry[userId];
    if (reg && reg.getCropper()) reg.getCropper().rotate(deg);
}

function flipCrop(userId, direction) {
    const reg = window.CropperRegistry[userId];
    if (!reg || !reg.getCropper()) return;
    const cropper = reg.getCropper();
    const data = cropper.getData();
    const scaleX = data.scaleX || 1;
    const scaleY = data.scaleY || 1;
    
    if (direction === 'h') {
        cropper.scaleX(-scaleX);
    } else {
        cropper.scaleY(-scaleY);
    }
}

function resetCrop(userId) {
    const reg = window.CropperRegistry[userId];
    if (reg && reg.getCropper()) reg.getCropper().reset();
}

function setZoom(userId, ratio) {
    const reg = window.CropperRegistry[userId];
    if (reg && reg.getCropper()) reg.getCropper().zoom(ratio);
}

function cancelCrop(userId) {
    const reg = window.CropperRegistry[userId];
    if (!reg) return;
    
    if (reg.getCropper()) {
        reg.getCropper().destroy();
        reg.setCropper(null);
    }
    reg.fileInput.value = '';
    reg.cropImage.src = '';
}

function applyCrop(userId) {
    const reg = window.CropperRegistry[userId];
    if (!reg || !reg.getCropper()) return;
    
    const cropper = reg.getCropper();
    
    // Get cropped canvas
    const canvas = cropper.getCroppedCanvas({
        width: 500,
        height: 500,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high',
    });
    
    canvas.toBlob(function(blob) {
        const fileName = 'cropped_' + Date.now() + '.jpg';
        const croppedFile = new File([blob], fileName, {
            type: 'image/jpeg',
            lastModified: Date.now(),
        });
        
        // Update file input
        const dt = new DataTransfer();
        dt.items.add(croppedFile);
        reg.fileInput.files = dt.files;
        
        // Update preview
        const previewUrl = URL.createObjectURL(blob);
        reg.previewImg.src = previewUrl;
        reg.previewImg.style.display = 'block';
        if (reg.placeholder) reg.placeholder.style.display = 'none';
        if (reg.removeBtn) reg.removeBtn.classList.remove('d-none');
        if (reg.removeFlag) reg.removeFlag.value = '0';
        
        // Cleanup
        cropper.destroy();
        reg.setCropper(null);
        reg.bsModal.hide();
        reg.cropImage.src = '';
    }, 'image/jpeg', 0.92);
}

function removePhoto(userId) {
    if (!confirm('Remove this photo?')) return;
    
    const reg = window.CropperRegistry[userId];
    if (!reg) return;
    
    reg.fileInput.value = '';
    if (reg.removeFlag) reg.removeFlag.value = '1';
    
    reg.previewImg.src = '';
    reg.previewImg.style.display = 'none';
    
    if (reg.placeholder) {
        reg.placeholder.style.display = 'block';
    }
    
    if (reg.removeBtn) reg.removeBtn.classList.add('d-none');
}

/* ============================================ */
/* AUTO-INIT ON DOM READY                      */
/* ============================================ */
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-photo-cropper]').forEach(function(el) {
        const userId = el.dataset.userId;
        const aspectRatio = parseFloat(el.dataset.aspectRatio) || 1;
        const maxSize = parseFloat(el.dataset.maxSize) || 2;
        
        if (userId) {
            initPhotoCropper(userId, aspectRatio, maxSize);
        }
    });
});
</script>
@endpush
@endonce