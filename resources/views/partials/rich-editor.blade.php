<!-- resources/views/partials/rich-editor.blade.php -->
@once
@push('styles')
<style>
/* TinyMCE custom styling to match Bootstrap */
.tox-tinymce {
    border-radius: 0.5rem !important;
    border: 1px solid #dee2e6 !important;
}
.tox-tinymce:focus-within {
    border-color: #4e73df !important;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25) !important;
}
.tox .tox-toolbar__primary {
    background: #f8f9fc !important;
}
.tox .tox-statusbar {
    background: #f8f9fc !important;
    border-top: 1px solid #dee2e6 !important;
}
</style>
@endpush

@push('scripts')
<script>
function initRichEditor(selector) {
    if (typeof tinymce === 'undefined') {
        console.warn('TinyMCE not loaded');
        return;
    }
    
    tinymce.init({
        selector: selector,
        height: 620,
        menubar: false,
        branding: false,
        promotion: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
            'preview', 'anchor', 'searchreplace', 'visualblocks', 'code',
            'fullscreen', 'insertdatetime', 'media', 'table', 'help',
            'wordcount', 'codesample',  'emoticons', 
             'autoresize',
        ],
        toolbar: [
            'undo redo | blocks | bold italic underline strikethrough | ' +
            'forecolor backcolor | alignleft aligncenter alignright alignjustify',
            'bullist numlist outdent indent | link image media table | ' +
            'subscript superscript charmap | codesample code | removeformat'
        ],
        toolbar_mode: 'wrap',
        content_style: `
            body { 
                font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif; 
                font-size: 14px;
                line-height: 1.6;
            }
           .math { font-family: 'Times New Roman', serif; font-style: italic; }
            p { margin: 0 0 8px 0; }
            img { max-width: 100%; height: auto; }
            table { border-collapse: collapse; width: 100%; }
            table td, table th { border: 1px solid #ccc; padding: 5px 8px; }
        `,
        images_upload_url: '{{ route("rich-editor.upload-image") }}',
        images_upload_credentials: true,
        automatic_uploads: true,
        file_picker_types: 'image',
        image_advtab: true,
        image_title: true,
        paste_data_images: true,
        convert_urls: false,
        setup: function(editor) {
            editor.on('change keyup', function() {
                editor.save();
            });
        }
    });
}

// Auto-init on DOM ready if selectors are marked
$(document).ready(function() {
    $('.rich-editor').each(function() {
        const id = $(this).attr('id');
        if (id && !tinymce.get(id)) {
            initRichEditor('#' + id);
        }
    });
});
</script>
@endpush
@endonce