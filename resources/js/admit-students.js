// resources/js/admit-students.js
$(document).ready(function() {
    // Auto-select class if parameter is present
    const urlParams = new URLSearchParams(window.location.search);
    const classId = urlParams.get('class');
    if (classId) {
        $('#currentClassSelect').val(classId).trigger('change');
    }
    
    // Update student count
    function updateStudentCount() {
        const count = $('.student-checkbox:checked').length;
        $('#selectedCount').text(`Selected: ${count} student(s)`);
        
        // Enable/disable submit button
        if (count > 0) {
            $('#submitBtn').prop('disabled', false);
        } else {
            $('#submitBtn').prop('disabled', true);
        }
    }
    
    // Handle checkbox changes
    $(document).on('change', '.student-checkbox', function() {
        updateStudentCount();
    });
    
    // Select all
    window.selectAllStudents = function() {
        $('.student-checkbox').prop('checked', true);
        updateStudentCount();
    };
    
    // Deselect all
    window.deselectAllStudents = function() {
        $('.student-checkbox').prop('checked', false);
        updateStudentCount();
    };
    
    // Initialize
    updateStudentCount();
});