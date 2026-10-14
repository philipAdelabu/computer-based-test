@extends('layouts.app')

@section('title', 'Edit Grading Scale')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('page-title', 'Edit Grading Scale')

@section('content')
<form action="{{ route('admin.grading.update', $scale->id) }}" method="POST">
    @csrf
    @method('PUT')
    
    <!-- Same structure as create.blade.php -->
    <!-- Pre-fill $scale->name, $scale->description, $scale->is_default -->
    <!-- Loop through $scale->items to populate rows -->
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Scale Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Scale Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" 
                               value="{{ old('name', $scale->name) }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $scale->description) }}</textarea>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_default" value="1" 
                               id="isDefault" {{ old('is_default', $scale->is_default) ? 'checked' : '' }}>
                        <label class="form-check-label" for="isDefault">
                            <strong>Set as Default</strong>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header bg-white d-flex justify-content-between">
            <h6 class="mb-0">Grade Ranges</h6>
            <button type="button" class="btn btn-sm btn-primary" onclick="addGradeRow()">
                <i class="bi bi-plus"></i> Add Grade
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
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
                        @foreach($scale->items as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <input type="number" name="items[{{ $loop->index }}][min_score]" 
                                           class="form-control form-control-sm" step="0.01"
                                           value="{{ $item->min_score }}" required>
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $loop->index }}][max_score]" 
                                           class="form-control form-control-sm" step="0.01"
                                           value="{{ $item->max_score }}" required>
                                </td>
                                <td>
                                    <input type="text" name="items[{{ $loop->index }}][grade]" 
                                           class="form-control form-control-sm"
                                           value="{{ $item->grade }}" required>
                                </td>
                                <td>
                                    <input type="text" name="items[{{ $loop->index }}][remark]" 
                                           class="form-control form-control-sm"
                                           value="{{ $item->remark }}">
                                </td>
                                <td>
                                    <select name="items[{{ $loop->index }}][color]" class="form-select form-select-sm">
                                        @foreach(['success', 'primary', 'info', 'warning', 'danger', 'secondary', 'dark'] as $color)
                                            <option value="{{ $color }}" {{ $item->color == $color ? 'selected' : '' }}>
                                                {{ $color }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="this.closest('tr').remove(); renumberRows();">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body text-center">
            <button type="submit" class="btn btn-primary px-5">
                <i class="bi bi-save"></i> Update
            </button>
            <a href="{{ route('admin.grading.index') }}" class="btn btn-secondary px-5">Cancel</a>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
const colors = ['success', 'primary', 'info', 'warning', 'danger', 'secondary', 'dark'];
let rowCount = {{ $scale->items->count() }};

function addGradeRow(data = {}) {
    rowCount++;
    const row = document.createElement('tr');
    row.innerHTML = `
        <td class="text-center">${rowCount}</td>
        <td><input type="number" name="items[${rowCount}][min_score]" class="form-control form-control-sm" step="0.01" min="0" max="100" required></td>
        <td><input type="number" name="items[${rowCount}][max_score]" class="form-control form-control-sm" step="0.01" min="0" max="100" required></td>
        <td><input type="text" name="items[${rowCount}][grade]" class="form-control form-control-sm" placeholder="A1" required></td>
        <td><input type="text" name="items[${rowCount}][remark]" class="form-control form-control-sm"></td>
        <td>
            <select name="items[${rowCount}][color]" class="form-select form-select-sm">
                ${colors.map(c => `<option value="${c}">${c}</option>`).join('')}
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
</script>
@endpush