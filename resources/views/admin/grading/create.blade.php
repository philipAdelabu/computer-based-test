@extends('layouts.app')

@section('title', 'Create Grading Scale')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('page-title', 'Create Grading Scale')

@section('content')
<form action="{{ route('admin.grading.store') }}" method="POST" id="gradingForm">
    @csrf
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Scale Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Scale Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" class="form-control" 
                               value="{{ old('name') }}" 
                               placeholder="e.g., WAEC Grading, University 5-Point" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="2"
                                  placeholder="Optional description">{{ old('description') }}</textarea>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_default" value="1" 
                               id="isDefault" {{ old('is_default') ? 'checked' : '' }}>
                        <label class="form-check-label" for="isDefault">
                            <strong>Set as Default</strong>
                            <br>
                            <small class="text-muted">This scale will be used automatically for grading</small>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Quick Templates</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">Click a template to auto-fill:</p>
                    <button type="button" class="btn btn-sm btn-outline-primary w-100 mb-2" 
                            onclick="loadTemplate('standard')">
                        <i class="bi bi-lightning"></i> Standard (A-F)
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary w-100 mb-2" 
                            onclick="loadTemplate('waec')">
                        <i class="bi bi-lightning"></i> WAEC (A1-F9)
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary w-100 mb-2" 
                            onclick="loadTemplate('university')">
                        <i class="bi bi-lightning"></i> University (5-Point)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Grade Ranges</h6>
            <button type="button" class="btn btn-sm btn-primary" onclick="addGradeRow()">
                <i class="bi bi-plus"></i> Add Grade
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" id="gradesTable">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th style="width: 130px;">Min Score</th>
                            <th style="width: 130px;">Max Score</th>
                            <th style="width: 150px;">Grade</th>
                            <th>Remark</th>
                            <th style="width: 140px;">Color</th>
                            <th style="width: 80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="gradesBody">
                        <!-- Rows added by JS -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div id="validationErrors" class="text-danger small"></div>
        </div>
    </div>

    <div class="card">
        <div class="card-body text-center">
            <button type="submit" class="btn btn-primary px-5">
                <i class="bi bi-save"></i> Create Grading Scale
            </button>
            <a href="{{ route('admin.grading.index') }}" class="btn btn-secondary px-5">
                Cancel
            </a>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
const colors = ['success', 'primary', 'info', 'warning', 'danger', 'secondary', 'dark'];

const templates = {
    standard: [
        { min: 80, max: 100, grade: 'A', remark: 'Excellent', color: 'success' },
        { min: 70, max: 79, grade: 'B', remark: 'Very Good', color: 'primary' },
        { min: 60, max: 69, grade: 'C', remark: 'Good', color: 'info' },
        { min: 50, max: 59, grade: 'D', remark: 'Fair', color: 'warning' },
        { min: 40, max: 49, grade: 'E', remark: 'Poor', color: 'warning' },
        { min: 0, max: 39, grade: 'F', remark: 'Fail', color: 'danger' },
    ],
    waec: [
        { min: 75, max: 100, grade: 'A1', remark: 'Excellent', color: 'success' },
        { min: 70, max: 74, grade: 'B2', remark: 'Very Good', color: 'success' },
        { min: 65, max: 69, grade: 'B3', remark: 'Good', color: 'primary' },
        { min: 60, max: 64, grade: 'C4', remark: 'Credit', color: 'primary' },
        { min: 55, max: 59, grade: 'C5', remark: 'Credit', color: 'info' },
        { min: 50, max: 54, grade: 'C6', remark: 'Credit', color: 'info' },
        { min: 45, max: 49, grade: 'D7', remark: 'Pass', color: 'warning' },
        { min: 40, max: 44, grade: 'E8', remark: 'Pass', color: 'warning' },
        { min: 0, max: 39, grade: 'F9', remark: 'Fail', color: 'danger' },
    ],
    university: [
        { min: 70, max: 100, grade: 'A', remark: 'First Class', color: 'success' },
        { min: 60, max: 69, grade: 'B', remark: 'Second Class Upper', color: 'primary' },
        { min: 50, max: 59, grade: 'C', remark: 'Second Class Lower', color: 'info' },
        { min: 45, max: 49, grade: 'D', remark: 'Third Class', color: 'warning' },
        { min: 40, max: 44, grade: 'E', remark: 'Pass', color: 'warning' },
        { min: 0, max: 39, grade: 'F', remark: 'Fail', color: 'danger' },
    ],
};

let rowCount = 0;

function addGradeRow(data = {}) {
    rowCount++;
    const row = document.createElement('tr');
    row.innerHTML = `
        <td class="text-center">${rowCount}</td>
        <td>
            <input type="number" name="items[${rowCount}][min_score]" 
                   class="form-control form-control-sm" step="0.01" min="0" max="100"
                   value="${data.min ?? ''}" required>
        </td>
        <td>
            <input type="number" name="items[${rowCount}][max_score]" 
                   class="form-control form-control-sm" step="0.01" min="0" max="100"
                   value="${data.max ?? ''}" required>
        </td>
        <td>
            <input type="text" name="items[${rowCount}][grade]" 
                   class="form-control form-control-sm" 
                   value="${data.grade ?? ''}" placeholder="A1" required>
        </td>
        <td>
            <input type="text" name="items[${rowCount}][remark]" 
                   class="form-control form-control-sm" 
                   value="${data.remark ?? ''}" placeholder="Excellent">
        </td>
        <td>
            <select name="items[${rowCount}][color]" class="form-select form-select-sm">
                ${colors.map(c => `<option value="${c}" ${data.color === c ? 'selected' : ''}>${c}</option>`).join('')}
            </select>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove(); renumberRows();">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    document.getElementById('gradesBody').appendChild(row);
}

function renumberRows() {
    $('#gradesBody tr').each(function(index) {
        $(this).find('td:first').text(index + 1);
    });
}

function loadTemplate(name) {
    if (!confirm('This will replace all current grade rows. Continue?')) return;
    
    $('#gradesBody').empty();
    rowCount = 0;
    
    const tpl = templates[name];
    if (tpl) {
        tpl.forEach(item => addGradeRow(item));
    }
}

// Initialize with one empty row
$(document).ready(function() {
    addGradeRow();
});
</script>
@endpush