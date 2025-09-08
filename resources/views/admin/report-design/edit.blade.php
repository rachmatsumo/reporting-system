@extends('layouts.user_type.auth')
@section('title', 'Edit Report Design')
@section('content')
<div class="container">
    <x-page-header route-prefix="report-designs" mode="index" />
    <form id="reportDesignForm" method="POST" action="{{ route('report-designs.update', $reportDesign) }}">
        @csrf
        @method('PUT')
        
        <!-- Basic Info -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Nama Report <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                    value="{{ old('name', $reportDesign->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $reportDesign->description) }}</textarea>
            </div>
        </div>

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" id="reportTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="main-fields-tab" data-bs-toggle="tab" 
                        data-bs-target="#main-fields" type="button" role="tab">
                    <i class="bi bi-file-post me-2"></i>Main Fields ({{ $reportDesign->fields->count() }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="sub-reports-tab" data-bs-toggle="tab" 
                        data-bs-target="#sub-reports" type="button" role="tab">
                    <i class="bi bi-file-earmark me-2"></i>Sub Reports ({{ $reportDesign->subDesigns->count() }})
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="reportTabsContent">
            <!-- Main Fields Tab -->
            <div class="tab-pane fade show active" id="main-fields" role="tabpanel">
                <div class="mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6>Main Report Fields</h6>
                        <button type="button" class="btn btn-success btn-sm" onclick="addMainField()">
                            <i class="bi bi-plus"></i> Tambah Field
                        </button>
                    </div>

                    <div id="mainFieldsContainer">
                        @foreach($reportDesign->fields as $field)
                        <div class="position-relative field-item border rounded p-3 mb-3" id="main-field-{{ $loop->index }}">
                            <input type="hidden" name="main_fields[{{ $loop->index }}][id]" value="{{ $field->id }}">
                            <div class="d-flex position-absolute top-0 start-1">
                                <span class="drag-handle text-muted" style="cursor: move;">
                                    <i class="bi bi-grip-horizontal"></i>
                                </span>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Label <span class="text-danger">*</span></label>
                                    <input type="text" name="main_fields[{{ $loop->index }}][label]" 
                                            class="form-control" value="{{ $field->label }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tipe Field <span class="text-danger">*</span></label>
                                    <select name="main_fields[{{ $loop->index }}][type]" class="form-select" 
                                            onchange="handleTypeChange('main-field-{{ $loop->index }}', {{ $loop->index }}, this.value, 'main_fields')" required>
                                        @foreach(['text' => 'Text', 'textarea' => 'Textarea', 'textarea_rich' => 'Textarea with Editor', 'number' => 'Number', 'file' => 'File', 'image' => 'Image', 'date' => 'Date', 'time' => 'Time', 'month' => 'Month', 'year' => 'Year', 'checkbox' => 'Checkbox', 'select' => 'Select', 'map' => 'Map', 'personnel' => 'Personnel', 'attendance' => 'Attendance'] as $value => $label)
                                            <option value="{{ $value }}" {{ $field->type === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Required</label>
                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="main_fields[{{ $loop->index }}][required]" value="1" 
                                                class="form-check-input" id="required-main-field-{{ $loop->index }}"
                                                {{ $field->required ? 'checked' : '' }}>
                                        <label class="form-check-label" for="required-main-field-{{ $loop->index }}">Ya</label>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-danger d-block" onclick="removeMainField({{ $loop->index }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-6" id="defaultValue-main-field-{{ $loop->index }}" 
                                        style="{{ $field->type === 'select' ? 'display: none;' : '' }}">
                                    <label class="form-label">Default Value</label>
                                    <input type="{{ $field->type === 'number' ? 'number' : ($field->type === 'date' ? 'date' : ($field->type === 'time' ? 'time' : ($field->type === 'month' ? 'month' : 'text'))) }}" 
                                            name="main_fields[{{ $loop->index }}][default_value]" 
                                            class="form-control" value="{{ $field->default_value }}">
                                </div>
                            </div>
                            
                            <div id="selectOptions-main-field-{{ $loop->index }}" 
                                    style="{{ $field->type === 'select' ? 'display: block;' : 'display: none;' }}" class="mt-3">
                                @php 
                                    $elIndex = $loop->index;
                                @endphp
                                <label class="form-label">Options untuk Select</label>
                                <div class="select-options-container" id="optionsContainer-main-field-{{ $loop->index }}">
                                    @if($field->type === 'select' && $field->options)
                                        @foreach($field->options as $optIndex => $option)
                                        <div class="row mb-2 select-option" id="option-main-field-{{ $elIndex }}-{{ $optIndex }}">
                                            <div class="col-md-4">
                                                <input type="text" name="main_fields[{{ $elIndex }}][options][{{ $optIndex }}][value]" 
                                                        class="form-control" placeholder="Value" value="{{ $option['value'] ?? '' }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" name="main_fields[{{ $elIndex }}][options][{{ $optIndex }}][label]" 
                                                        class="form-control" placeholder="Label" value="{{ $option['label'] ?? '' }}" required>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-danger btn-sm" onclick="removeSelectOption('main-field-{{ $elIndex }}', {{ $optIndex }})">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @endforeach
                                    @endif
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addSelectOption('main-field-{{ $loop->index }}', {{ $loop->index }}, 'main_fields')">
                                    <i class="bi bi-plus"></i> Tambah Option
                                </button>
                            </div>
                            
                            <input type="hidden" name="main_fields[{{ $loop->index }}][name]" value="{{ $field->name }}" class="field-name-input">
                            <input type="hidden" name="main_fields[{{ $loop->index }}][order_index]" value="{{ $field->order_index }}" class="field-order">
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sub Reports Tab -->
            <div class="tab-pane fade" id="sub-reports" role="tabpanel">
                <div class="mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6>Sub Reports</h6>
                        <button type="button" class="btn btn-success btn-sm" onclick="addSubReport()">
                            <i class="bi bi-plus"></i> Tambah Sub Report
                        </button>
                    </div>

                    <div id="subReportsContainer">
                        @foreach($reportDesign->subDesigns as $subDesign)
                        <div class="sub-report-card" id="sub-report-{{ $loop->index }}">
                            <input type="hidden" name="sub_reports[{{ $loop->index }}][id]" value="{{ $subDesign->id }}">
                            <div class="sub-report-header">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <input type="text" name="sub_reports[{{ $loop->index }}][name]" 
                                                class="form-control" placeholder="Nama Sub Report" 
                                                value="{{ $subDesign->name }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="sub_reports[{{ $loop->index }}][type]" class="form-select" required>
                                            @foreach(['form' => 'Form', 'checklist' => 'Checklist', 'table' => 'Table', 'custom' => 'Custom'] as $value => $label)
                                                <option value="{{ $value }}" {{ $subDesign->type === $value ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="sub_reports[{{ $loop->index }}][description]" 
                                                class="form-control" placeholder="Deskripsi (opsional)"
                                                value="{{ $subDesign->description }}">
                                    </div>
                                    <div class="col-md-1 text-end">
                                        <button type="button" class="btn btn-outline-secondary btn-sm me-2" 
                                                onclick="toggleSubReportCollapse({{ $loop->index }})" data-bs-toggle="collapse" 
                                                data-bs-target="#subReportContent-{{ $loop->index }}">
                                            <i class="bi bi-chevron-down"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="removeSubReport({{ $loop->index }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="collapse show" id="subReportContent-{{ $loop->index }}">
                                <div class="p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <small class="text-muted">Fields untuk sub report ini: ({{ $subDesign->fields->count() }} fields)</small>
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addSubField({{ $loop->index }})">
                                            <i class="bi bi-plus"></i> Tambah Field
                                        </button>
                                    </div>
                                    
                                    <div id="subFieldsContainer-{{ $loop->index }}">
                                        @foreach($subDesign->fields as $subField)
                                        <div class="position-relative field-item border rounded p-3 mb-3" id="sub-field-{{ $loop->parent->index }}-{{ $loop->index }}">
                                            <input type="hidden" name="sub_reports[{{ $loop->parent->index }}][fields][{{ $loop->index }}][id]" value="{{ $subField->id }}">
                                            <div class="d-flex position-absolute top-0 start-1">
                                                <span class="drag-handle text-muted" style="cursor: move;">
                                                    <i class="bi bi-grip-horizontal"></i>
                                                </span>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label">Label <span class="text-danger">*</span></label>
                                                    <input type="text" name="sub_reports[{{ $loop->parent->index }}][fields][{{ $loop->index }}][label]" 
                                                            class="form-control" value="{{ $subField->label }}" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Tipe Field <span class="text-danger">*</span></label>
                                                    <select name="sub_reports[{{ $loop->parent->index }}][fields][{{ $loop->index }}][type]" class="form-select" 
                                                            onchange="handleTypeChange('sub-field-{{ $loop->parent->index }}-{{ $loop->index }}', {{ $loop->index }}, this.value, 'sub_reports[{{ $loop->parent->index }}][fields]')" required>
                                                        @foreach(['text' => 'Text', 'textarea' => 'Textarea', 'textarea_rich' => 'Textarea with Editor', 'number' => 'Number', 'file' => 'File', 'image' => 'Image', 'date' => 'Date', 'time' => 'Time', 'month' => 'Month', 'year' => 'Year', 'checkbox' => 'Checkbox', 'select' => 'Select', 'map' => 'Map', 'personnel' => 'Personnel', 'attendance' => 'Attendance'] as $value => $label)
                                                            <option value="{{ $value }}" {{ $subField->type === $value ? 'selected' : '' }}>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Required</label>
                                                    <div class="form-check mt-2">
                                                        <input type="checkbox" name="sub_reports[{{ $loop->parent->index }}][fields][{{ $loop->index }}][required]" value="1" 
                                                                class="form-check-input" id="required-sub-field-{{ $loop->parent->index }}-{{ $loop->index }}"
                                                                {{ $subField->required ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="required-sub-field-{{ $loop->parent->index }}-{{ $loop->index }}">Ya</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <label class="form-label">&nbsp;</label>
                                                    <button type="button" class="btn btn-danger d-block" onclick="removeSubField({{ $loop->index }}, {{ $loop->parent->index }})">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <div class="row mt-3">
                                                <div class="col-md-6" id="defaultValue-sub-field-{{ $loop->parent->index }}-{{ $loop->index }}"
                                                        style="{{ $subField->type === 'select' ? 'display: none;' : '' }}">
                                                    <label class="form-label">Default Value</label>
                                                    <input type="{{ $subField->type === 'number' ? 'number' : ($subField->type === 'date' ? 'date' : ($subField->type === 'time' ? 'time' : ($subField->type === 'month' ? 'month' : 'text'))) }}" 
                                                            name="sub_reports[{{ $loop->parent->index }}][fields][{{ $loop->index }}][default_value]" 
                                                            class="form-control" value="{{ $subField->default_value }}">
                                                </div>
                                            </div>
                                            
                                            {{-- cek ini --}}
                                            @php
                                                $makeIndex = 0;
                                            @endphp
                                            <div id="selectOptions-sub-field-{{ $loop->parent->index }}-{{ $loop->index }}" 
                                                    style="{{ $subField->type === 'select' ? 'display: block;' : 'display: none;' }}" class="mt-3">
                                                    @php
                                                        $subElIndex = $loop->parent->index;
                                                    @endphp
                                                <label class="form-label">Options untuk Select</label>
                                                <div class="select-options-container" id="optionsContainer-sub-field-{{ $subElIndex }}-{{ $loop->index }}">
                                                    @if($subField->type === 'select' && $subField->options)
                                                        @foreach($subField->options as $optIndex => $option)  
                                                        <div class="row mb-2 select-option" id="option-sub-field-{{ $subElIndex }}-{{ $loop->parent->index }}-{{ $makeIndex }}">
                                                            <div class="col-md-4">
                                                                <input type="text" name="sub_reports[{{ $subElIndex }}][fields][{{ $loop->parent->index }}][options][{{ $makeIndex }}][value]" 
                                                                        class="form-control" placeholder="Value" value="{{ $option['value'] ?? '' }}" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" name="sub_reports[{{ $subElIndex }}][fields][{{ $loop->parent->index }}][options][{{ $makeIndex }}][label]" 
                                                                        class="form-control" placeholder="Label" value="{{ $option['label'] ?? '' }}" required>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button type="button" class="btn btn-danger btn-sm" onclick="removeSelectOption('sub-field-{{ $loop->parent->index }}-{{ $loop->index }}', {{ $makeIndex }})">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        @php 
                                                            $makeIndex++;
                                                        @endphp
                                                        @endforeach
                                                    @endif
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addSelectOption('sub-field-{{ $loop->parent->index }}-{{ $loop->index }}', {{ $loop->index }}, 'sub_reports[{{ $loop->parent->index }}][fields]')">
                                                    <i class="bi bi-plus"></i> Tambah Option
                                                </button>
                                            </div>
                                            
                                            <input type="hidden" name="sub_reports[{{ $loop->parent->index }}][fields][{{ $loop->index }}][name]" value="{{ $subField->name }}" class="field-name-input">
                                            <input type="hidden" name="sub_reports[{{ $loop->parent->index }}][fields][{{ $loop->index }}][order_index]" value="{{ $subField->order_index }}" class="field-order">
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            <input type="hidden" name="sub_reports[{{ $loop->index }}][order_index]" value="{{ $subDesign->order_index }}" class="sub-report-order">
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <x-form-buttons route-prefix="report-designs" mode="edit" />
        
    </form> 

</div>
 
@endsection

@push('styles')
<style>
    .field-item {
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }

    .field-item:hover {
        background-color: #e9ecef;
    }

    .sub-report-card {
        border: 2px solid #dee2e6;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .sub-report-header {
        background-color: #f1f3f4;
        padding: 15px;
        border-bottom: 1px solid #dee2e6;
        border-radius: 6px 6px 0 0;
    }

    .sortable-ghost {
        opacity: 0.4;
    }

    .drag-handle {
        cursor: move;
        padding: 5px;
    }

    .drag-handle:hover {
        color: #007bff !important;
    }

    .collapse-icon {
        transition: transform 0.3s ease;
    }

    .collapsed .collapse-icon {
        transform: rotate(-90deg);
    }
</style>
@endpush

@push('scripts')
<script>
let mainFieldCounter = {{ $reportDesign->fields->count() }};
let subReportCounter = {{ $reportDesign->subDesigns->count() }};
let subFieldCounters = {
    @foreach($reportDesign->subDesigns as $subDesign)
    {{ $loop->index }}: {{ $subDesign->fields->count() }},
    @endforeach
}; // Track field counters for each sub-report

const fieldTypes = [
    { value: 'text', label: 'Text' },
    { value: 'textarea', label: 'Textarea' },
    { value: 'textarea_rich', label: 'Textarea with Editor' },
    { value: 'number', label: 'Number' },
    { value: 'file', label: 'File' },
    { value: 'image', label: 'Image' },
    { value: 'date', label: 'Date' },
    { value: 'time', label: 'Time' },
    { value: 'month', label: 'Month' },
    { value: 'year', label: 'Year' },
    { value: 'checkbox', label: 'Checkbox' },
    { value: 'select', label: 'Select' },
    { value: 'map', label: 'Map' },
    { value: 'personnel', label: 'Personnel' },
    { value: 'attendance', label: 'Attendance' }
];

const subReportTypes = [
    { value: 'form', label: 'Form' },
    { value: 'checklist', label: 'Checklist' },
    { value: 'table', label: 'Table' },
    { value: 'custom', label: 'Custom' }
];

// Main Fields Functions
function addMainField() {
    const container = document.getElementById('mainFieldsContainer');
    const fieldId = mainFieldCounter++;
    
    const fieldHtml = createFieldHtml(fieldId, 'main_fields', 'removeMainField');
    container.insertAdjacentHTML('beforeend', fieldHtml);
    updateTabCounts();
}

function removeMainField(fieldId) {
    const fieldElement = document.getElementById(`main-field-${fieldId}`);
    if (fieldElement) {
        fieldElement.remove();
        updateTabCounts();
    }
}

// Sub Report Functions
function addSubReport() {
    const container = document.getElementById('subReportsContainer');
    const subReportId = subReportCounter++;
    subFieldCounters[subReportId] = 0;
    
    const subReportHtml = `
    <div class="sub-report-card" id="sub-report-${subReportId}">
        <input type="hidden" name="sub_reports[${subReportId}][id]" value="0">
            <div class="sub-report-header">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <input type="text" name="sub_reports[${subReportId}][name]" 
                               class="form-control" placeholder="Nama Sub Report" required>
                    </div>
                    <div class="col-md-3">
                        <select name="sub_reports[${subReportId}][type]" class="form-select" required>
                            ${subReportTypes.map(type => `<option value="${type.value}">${type.label}</option>`).join('')}
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="sub_reports[${subReportId}][description]" 
                               class="form-control" placeholder="Deskripsi (opsional)">
                    </div>
                    <div class="col-md-1 text-end">
                        <button type="button" class="btn btn-outline-secondary btn-sm me-2" 
                                onclick="toggleSubReportCollapse(${subReportId})" data-bs-toggle="collapse" 
                                data-bs-target="#subReportContent-${subReportId}">
                            <i class="bi bi-three-dots-vertical collapse-icon"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeSubReport(${subReportId})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="collapse show" id="subReportContent-${subReportId}">
                <div class="p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <small class="text-muted">Fields untuk sub report ini:</small>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addSubField(${subReportId})">
                            <i class="bi bi-plus"></i> Tambah Field
                        </button>
                    </div>
                    
                    <div id="subFieldsContainer-${subReportId}">
                        <!-- Sub fields akan ditambahkan di sini -->
                    </div>
                </div>
            </div>
            
            <input type="hidden" name="sub_reports[${subReportId}][order_index]" value="${subReportId}" class="sub-report-order">
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', subReportHtml);
    initializeSubFieldSortable(subReportId);
    updateTabCounts();
}

function removeSubReport(subReportId) {
    const subReportElement = document.getElementById(`sub-report-${subReportId}`);
    if (subReportElement) {
        subReportElement.remove();
        delete subFieldCounters[subReportId];
        updateTabCounts();
    }
}

// function toggleSubReportCollapse(subReportId) {
//     const icon = document.querySelector(`[onclick="toggleSubReportCollapse(${subReportId})"] .collapse-icon`);
//     const content = document.getElementById(`subReportContent-${subReportId}`);
    
//     if (content.classList.contains('show')) {
//         // icon.style.transform = 'rotate(-90deg)';
//         icon.classList.remove('bi-chevron-down');
//         icon.classList.add('bi-chevron-up');
//     } else {
//         icon.classList.remove('bi-chevron-up');
//         icon.classList.add('bi-chevron-down');
//     }
// }

// Sub Field Functions
function addSubField(subReportId) {
    const container = document.getElementById(`subFieldsContainer-${subReportId}`);
    const fieldId = subFieldCounters[subReportId]++;
    
    const fieldHtml = createFieldHtml(fieldId, `sub_reports[${subReportId}][fields]`, 'removeSubField', subReportId);
    container.insertAdjacentHTML('beforeend', fieldHtml);
    
    // Re-initialize sortable if this is the first field
    if (container.children.length === 1) {
        initializeSubFieldSortable(subReportId);
    }
}

function removeSubField(fieldId, subReportId) {
    const fieldElement = document.getElementById(`sub-field-${subReportId}-${fieldId}`);
    if (fieldElement) {
        fieldElement.remove();
    }
}

// Generic Field Creation Function
function createFieldHtml(fieldId, namePrefix, removeFunction, subReportId = null) {
    const elementId = subReportId !== null ? `sub-field-${subReportId}-${fieldId}` : `main-field-${fieldId}`;
    const removeCall = subReportId !== null ? `${removeFunction}(${fieldId}, ${subReportId})` : `${removeFunction}(${fieldId})`;
    
    return `

        <div class="position-relative field-item border rounded p-3 mb-3" id="${elementId}">
            <input type="hidden" name="${elementId}[id]" value="0">
            <div class="d-flex position-absolute top-0 start-1">
                <span class="drag-handle text-muted" style="cursor: move;">
                    <i class="bi bi-grip-horizontal"></i>
                </span>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Label <span class="text-danger">*</span></label>
                    <input type="text" name="${namePrefix}[${fieldId}][label]" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tipe Field <span class="text-danger">*</span></label>
                    <select name="${namePrefix}[${fieldId}][type]" class="form-select" 
                            onchange="handleTypeChange('${elementId}', ${fieldId}, this.value, '${namePrefix}')" required>
                        ${fieldTypes.map(type => `<option value="${type.value}">${type.label}</option>`).join('')}
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Required</label>
                    <div class="form-check mt-2">
                        <input type="checkbox" name="${namePrefix}[${fieldId}][required]" value="1" 
                               class="form-check-input" id="required-${elementId}">
                        <label class="form-check-label" for="required-${elementId}">Ya</label>
                    </div>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-danger d-block" onclick="${removeCall}">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-6" id="defaultValue-${elementId}">
                    <label class="form-label">Default Value</label>
                    <input type="text" name="${namePrefix}[${fieldId}][default_value]" class="form-control">
                </div>
            </div>
            
            <div id="selectOptions-${elementId}" style="display: none;" class="mt-3">
                <label class="form-label">Options untuk Select</label>
                <div class="select-options-container" id="optionsContainer-${elementId}">
                    <!-- Options akan ditambahkan di sini -->
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addSelectOption('${elementId}', ${fieldId}, '${namePrefix}')">
                    <i class="bi bi-plus"></i> Tambah Option
                </button>
            </div>
            
            <input type="hidden" name="${namePrefix}[${fieldId}][name]" class="field-name-input">
            <input type="hidden" name="${namePrefix}[${fieldId}][order_index]" value="${fieldId}" class="field-order">
        </div>
    `;
}

function handleTypeChange(elementId, fieldId, type, namePrefix) {
    const defaultValueContainer = document.getElementById(`defaultValue-${elementId}`);
    const selectOptionsContainer = document.getElementById(`selectOptions-${elementId}`);
    
    if (type === 'select') {
        defaultValueContainer.style.display = 'none';
        selectOptionsContainer.style.display = 'block';
        
        // Add first option if none exists
        const optionsContainer = document.getElementById(`optionsContainer-${elementId}`);
        if (optionsContainer.children.length === 0) {
            addSelectOption(elementId, fieldId, namePrefix);
        }
    } else {
        defaultValueContainer.style.display = 'block';
        selectOptionsContainer.style.display = 'none';
        
        // Update input type based on field type
        const defaultInput = defaultValueContainer.querySelector('input');
        switch(type) {
            case 'number':
                defaultInput.type = 'number';
                break;
            case 'date':
                defaultInput.type = 'date';
                break;
            case 'time':
                defaultInput.type = 'time';
                break;
            case 'month':
                defaultInput.type = 'month';
                break;
            case 'checkbox':
                defaultInput.type = 'checkbox';
                defaultInput.value = '1';
                break;
            default:
                defaultInput.type = 'text';
        }
    }
}

function addSelectOption(elementId, fieldId, namePrefix) {
    const container = document.getElementById(`optionsContainer-${elementId}`);
    const optionIndex = container.children.length;
    
    const optionHtml = `
        <div class="row mb-2 select-option" id="option-${elementId}-${optionIndex}">
            <div class="col-md-4">
                <input type="text" name="${namePrefix}[${fieldId}][options][${optionIndex}][value]" 
                       class="form-control" placeholder="Value" required>
            </div>
            <div class="col-md-6">
                <input type="text" name="${namePrefix}[${fieldId}][options][${optionIndex}][label]" 
                       class="form-control" placeholder="Label" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeSelectOption('${elementId}', ${optionIndex})">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', optionHtml);
}

function removeSelectOption(elementId, optionIndex) {
    const optionElement = document.getElementById(`option-${elementId}-${optionIndex}`);
    if (optionElement) {
        optionElement.remove();
    }
}

// Generate field names from labels
function generateFieldName(label) {
    return label.toLowerCase()
                .replace(/[^a-z0-9\s]/g, '')
                .replace(/\s+/g, '_')
                .substring(0, 50);
}

// Update field names when labels change
function updateFieldNames() {
    // Update main fields
    document.querySelectorAll('#mainFieldsContainer .field-item').forEach(field => {
        const labelInput = field.querySelector('[name*="[label]"]');
        const nameInput = field.querySelector('.field-name-input');
        
        if (labelInput && nameInput && labelInput.value) {
            nameInput.value = generateFieldName(labelInput.value);
        }
    });
    
    // Update sub report fields
    document.querySelectorAll('[id^="subFieldsContainer-"] .field-item').forEach(field => {
        const labelInput = field.querySelector('[name*="[label]"]');
        const nameInput = field.querySelector('.field-name-input');
        
        if (labelInput && nameInput && labelInput.value) {
            nameInput.value = generateFieldName(labelInput.value);
        }
    });
}

// Update tab counts
function updateTabCounts() {
    const mainFieldsCount = document.querySelectorAll('#mainFieldsContainer .field-item').length;
    const subReportsCount = document.querySelectorAll('#subReportsContainer .sub-report-card').length;
    
    document.getElementById('main-fields-tab').innerHTML = `<i class="bi bi-file-post me-2"></i>Main Fields (${mainFieldsCount})`;
    document.getElementById('sub-reports-tab').innerHTML = `<i class="bi bi-file-earmark me-2"></i>Sub Reports (${subReportsCount})`;
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize sortable for main fields
    if (document.getElementById('mainFieldsContainer')) {
        new Sortable(document.getElementById('mainFieldsContainer'), {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'sortable-ghost',
            onEnd: function() {
                updateFieldOrders();
            }
        });
    }

    // Initialize sortable for sub reports
    if (document.getElementById('subReportsContainer')) {
        new Sortable(document.getElementById('subReportsContainer'), {
            animation: 150,
            handle: '.sub-report-header',
            ghostClass: 'sortable-ghost',
            onEnd: function() {
                updateSubReportOrders();
            }
        });
    }

    // Initialize sortable for existing sub-report fields
    @foreach($reportDesign->subDesigns as $subDesign)
    initializeSubFieldSortable({{ $loop->index }});
    @endforeach

    // Update field names and orders before submit
    document.getElementById('reportDesignForm').addEventListener('submit', function(e) {
        updateFieldNames();
        updateFieldOrders();
        updateSubReportOrders();
    });

    // Auto-generate field names when labels change
    document.addEventListener('input', function(e) {
        if (e.target.name && e.target.name.includes('[label]')) {
            const fieldItem = e.target.closest('.field-item');
            const nameInput = fieldItem.querySelector('.field-name-input');
            if (nameInput && !nameInput.value) { // Only auto-generate if name is empty
                nameInput.value = generateFieldName(e.target.value);
            }
        }
    });

    // Initialize tab counts
    updateTabCounts();
});

function updateFieldOrders() {
    // Update main fields order
    document.querySelectorAll('#mainFieldsContainer .field-item').forEach((el, index) => {
        const orderInput = el.querySelector('.field-order');
        if (orderInput) {
            orderInput.value = index;
        }
    });
    
    // Update sub report fields order
    document.querySelectorAll('[id^="subFieldsContainer-"]').forEach(container => {
        container.querySelectorAll('.field-item').forEach((el, index) => {
            const orderInput = el.querySelector('.field-order');
            if (orderInput) {
                orderInput.value = index;
            }
        });
    });
}

function updateSubReportOrders() {
    document.querySelectorAll('#subReportsContainer .sub-report-card').forEach((el, index) => {
        const orderInput = el.querySelector('.sub-report-order');
        if (orderInput) {
            orderInput.value = index;
        }
    });
}

// Dynamic sortable initialization for sub-report fields
function initializeSubFieldSortable(subReportId) {
    const container = document.getElementById(`subFieldsContainer-${subReportId}`);
    if (container) {
        new Sortable(container, {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'sortable-ghost',
            onEnd: function() {
                updateFieldOrders();
            }
        });
    }
}
</script> 
@endpush