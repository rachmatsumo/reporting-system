@extends('layouts.user_type.auth')
 
@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-2"> 
                            <div class="d-flex flex-column">
                                <h5 class="mb-0">Edit Report : {{ $report->reportDesign->name }}</h5>
                                <p class="mb-0 text-muted">{{ $report->reportDesign->description }}</p>
                            </div>
                            <button class="btn btn-info mb-0" data-bs-toggle="modal" data-bs-target="#modal-scripts">
                                <i class="fas fa-code me-2"></i>Running Script
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('reports.update', $report) }}" enctype="multipart/form-data" id="reportForm">
                            @csrf
                            @method('PUT')
                            
                            <!-- Title -->
                            <div class="row mb-4">
                                {{-- <div class="col-md-8">
                                    <label class="form-label">Judul Report <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                        value="{{ old('title', $report->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div> --}}
                                <div class="col-md-4">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="draft" {{ $report->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="submitted" {{ $report->status == 'submitted' ? 'selected' : '' }}>Submit</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Tab Navigation -->
                            <ul class="nav nav-tabs" id="reportTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="main-data-tab" data-bs-toggle="tab" 
                                            data-bs-target="#main-data" type="button" role="tab">
                                        <i class="fas fa-file-alt me-2"></i>General Information
                                    </button>
                                </li>
                                @foreach($report->reportDesign->subDesigns as $subDesign)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="sub-{{ $subDesign->id }}-tab" data-bs-toggle="tab" 
                                            data-bs-target="#sub-{{ $subDesign->id }}" type="button" role="tab">
                                        <i class="fas fa-layer-group me-2"></i>{{ $subDesign->name }}
                                        <span class="badge bg-secondary ms-1" id="count-{{ $subDesign->id }}">{{ $report->subData->where('report_sub_design_id', $subDesign->id)->count() }}</span>
                                    </button>
                                </li>
                                @endforeach
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content" id="reportTabsContent">
                                <!-- Main Data Tab -->
                                <div class="tab-pane fade show active" id="main-data" role="tabpanel">
                                    <div class="mt-4">
                                        @if($report->reportDesign->fields->count() > 0)
                                            <div class="row">
                                                @foreach($report->reportDesign->fields as $field)
                                                @php
                                                    $fieldValue = $report->data[$field->name] ?? null;
                                                @endphp
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">
                                                        {{ $field->label }}
                                                        @if($field->required)
                                                            <span class="text-danger">*</span>
                                                        @endif
                                                    </label>
                                                    
                                                    @include('reports.partials.field-input', [
                                                        'field' => $field, 
                                                        'name' => "main_data[{$field->name}]",
                                                        'value' => $fieldValue,
                                                    ])
                                                    
                                                    @error("main_data.{$field->name}")
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-5">
                                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">Tidak ada main fields yang tersedia</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Sub Reports Tabs -->
                                @foreach($report->reportDesign->subDesigns as $subDesign)
                                <div class="tab-pane fade" id="sub-{{ $subDesign->id }}" role="tabpanel">
                                    <div class="mt-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h6 class="mb-0">{{ $subDesign->name }}</h6>
                                                @if($subDesign->description)
                                                    <small class="text-muted">{{ $subDesign->description }}</small>
                                                @endif
                                            </div>
                                            <button type="button" class="btn btn-primary btn-sm" onclick="addSubReportRow({{ $subDesign->id }})">
                                                <i class="fas fa-plus"></i> 
                                                @if($subDesign->type === 'table')
                                                    Tambah Row
                                                @elseif($subDesign->type === 'checklist')
                                                    Tambah Item
                                                @else
                                                    Tambah Data
                                                @endif
                                            </button>
                                        </div>

                                        @if($subDesign->fields->count() > 0)
                                            @if($subDesign->type === 'table')
                                                <!-- Table Layout -->
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th width="5%">#</th>
                                                                @foreach($subDesign->fields as $subField)
                                                                    <th>
                                                                        {{ $subField->label }}
                                                                        @if($subField->required)
                                                                            <span class="text-danger">*</span>
                                                                        @endif
                                                                    </th>
                                                                @endforeach
                                                                <th width="10%">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="subReportTableBody-{{ $subDesign->id }}">
                                                            @php
                                                                $existingSubData = $report->subData->where('report_sub_design_id', $subDesign->id);
                                                            @endphp
                                                            @foreach($existingSubData as $index => $subData)
                                                            <tr id="subReportRow-{{ $subDesign->id }}-{{ $index }}">
                                                                <td>{{ $index + 1 }}</td>
                                                                @foreach($subDesign->fields as $subField)
                                                                <td>
                                                                    @include('reports.partials.field-input', [
                                                                        'field' => $subField,
                                                                        'name' => "sub_data[{$subDesign->id}][{$index}][{$subField->name}]",
                                                                        'value' => $subData->data[$subField->name] ?? null,
                                                                        'isTableCell' => true
                                                                    ])
                                                                </td>
                                                                @endforeach
                                                                <td>
                                                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeSubReportRow({{ $subDesign->id }}, {{ $index }})">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <!-- Form/Card Layout -->
                                                <div id="subReportCards-{{ $subDesign->id }}">
                                                    @php
                                                        $existingSubData = $report->subData->where('report_sub_design_id', $subDesign->id);
                                                    @endphp
                                                    @foreach($existingSubData as $index => $subData)
                                                    <div class="card mb-3" id="subReportCard-{{ $subDesign->id }}-{{ $index }}">
                                                        <div class="card-header d-flex justify-content-between align-items-center">
                                                            <h6 class="mb-0">{{ $subDesign->name }} #{{ $index + 1 }}</h6>
                                                            <button type="button" class="btn btn-danger btn-sm" onclick="removeSubReportRow({{ $subDesign->id }}, {{ $index }})">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                @foreach($subDesign->fields as $subField)
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label">
                                                                        {{ $subField->label }}
                                                                        @if($subField->required)
                                                                            <span class="text-danger">*</span>
                                                                        @endif
                                                                    </label>
                                                                    @include('reports.partials.field-input', [
                                                                        'field' => $subField,
                                                                        'name' => "sub_data[{$subDesign->id}][{$index}][{$subField->name}]",
                                                                        'value' => $subData->data[$subField->name] ?? null
                                                                    ])
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        @else
                                            <div class="text-center py-5">
                                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">Sub report ini belum memiliki fields</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div class="text-end mt-5">
                                <a href="{{ route('reports.index') }}" class="btn btn-secondary">Batal</a>
                                <button type="submit" name="action" value="draft" class="btn btn-outline-primary">Simpan sebagai Draft</button>
                                <button type="submit" name="action" value="submit" class="btn btn-primary">Submit Report</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('reports.partials.modal-scripts', ['scripts' => $scripts ?? []])

</main>

<script>
const attendanceCounters = {};
let currentAttendanceField = '';
let attendanceUsers = [];
let attendanceData = {};

// Inisialisasi data users (ini akan diisi dari controller)
const allUsers = [
    @foreach($users ?? [] as $user)
    {
        id: {{ $user->id }},
        name: '{{ $user->name }}',
        position: '{{ $user->position ?? "Staff" }}'
    },
    @endforeach
];

// Initialize counters for attendance fields
document.addEventListener('DOMContentLoaded', function() {
    // Initialize attendance counters
    document.querySelectorAll('[id^="attendanceTableBody-"]').forEach(tbody => {
        const fieldName = tbody.id.replace('attendanceTableBody-', '');
        attendanceCounters[fieldName] = tbody.querySelectorAll('tr').length;
    });
});

function slugify(str) {
    return str
        .toString()
        .toLowerCase()
        .trim() // hapus spasi depan-belakang
        .replace(/[^a-z0-9]+/g, '-') // ganti non-alfanumerik dengan -
        .replace(/^-+|-+$/g, '');    // hapus - di depan & belakang
}

function addAttendanceRow(fieldSlug, nameField) {
    // console.log('cek', fieldSlug, nameField);
    const tbody = document.getElementById(`attendanceTableBody-${fieldSlug}`);
    if (!tbody) {
        console.error("Tbody not found:", `attendanceTableBody-${fieldSlug}`);
        return;
    }

    if (!attendanceCounters[fieldSlug]) {
        attendanceCounters[fieldSlug] = 0;
    }

    const rowIndex = attendanceCounters[fieldSlug]++;
    
    const rowHtml = `
        <tr id="attendanceRow-${fieldSlug}-${rowIndex}">
            <td>${rowIndex + 1}</td>
            <td>
                <select name="attendance[${fieldSlug}][${rowIndex}][user_id]" class="form-select form-select-sm" required>
                    <option value="">-- Pilih Personnel --</option>
                    @foreach($users ?? [] as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <select name="attendance[${fieldSlug}][${rowIndex}][status]" class="form-select form-select-sm" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="hadir">Hadir</option>
                    <option value="tidak_hadir">Tidak Hadir</option>
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                </select>
            </td>
            <td>
                <input type="text" name="attendance[${fieldSlug}][${rowIndex}][note]" 
                       class="form-control form-control-sm" 
                       placeholder="Keterangan...">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" 
                        onclick="removeAttendanceRow('${fieldSlug}', ${rowIndex})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
    
    tbody.insertAdjacentHTML('beforeend', rowHtml);
    updateAttendanceSummary(fieldSlug, nameField);
}

function removeAttendanceRow(fieldSlug, rowIndex) {
    const row = document.getElementById(`attendanceRow-${fieldSlug}-${rowIndex}`);
    if (row) {
        row.remove();
        reorderAttendanceRows(fieldSlug);
    }
}

function reorderAttendanceRows(fieldSlug) {
    const tbody = document.getElementById(`attendanceTableBody-${fieldSlug}`);
    const rows = tbody.querySelectorAll('tr');
    
    rows.forEach((row, index) => {
        const numberCell = row.querySelector('td:first-child');
        if (numberCell) {
            numberCell.textContent = index + 1;
        }
    });
    
    // Update field name for proper form submission
    const originalFieldName = fieldSlug.replace(/_/g, '[').replace(/([a-zA-Z0-9]+)\[/g, '$1[') + ']';
    updateAttendanceSummary(originalFieldName);
}

function updateAttendanceSummary(fieldName, nameField) { 
    console.log(fieldName, nameField);
    const fieldSlug = fieldName.replace(/[^a-zA-Z0-9]/g, '-');
    const tbody = document.getElementById(`attendanceTableBody-${fieldSlug}`);
    // console.log('attendanceTableBody-', fieldSlug);
    const rows = tbody.querySelectorAll('tr');
    
    let summary = {
        rows: [],
        total: rows.length,
        hadir: 0,
        tidak_hadir: 0,
        izin: 0,
        sakit: 0
    };
    
    rows.forEach((row, index) => {
        const userSelect = row.querySelector('select[name*="[user_id]"]');
        const statusSelect = row.querySelector('select[name*="[status]"]');
        const noteInput = row.querySelector('input[name*="[note]"]');
        
        if (userSelect && statusSelect) {
            const rowData = {
                user_id: userSelect.value,
                status: statusSelect.value,
                note: noteInput ? noteInput.value : ''
            };
            
            summary.rows.push(rowData);
            
            if (statusSelect.value) {
                summary[statusSelect.value]++;
            }
        }
    }); 

    $('input[name="' + nameField + '"]').val(JSON.stringify(summary));


    // console.log('Updated attendance summary for', nameField, summary);
}

// Dengarkan semua perubahan di semua select/input dalam tbody attendanceTableBody-*
$(document).on('change', 'tbody[id^="attendanceTableBody-"] select, tbody[id^="attendanceTableBody-"] input', function () {
    const tbody = $(this).closest('tbody');  
    const tbodyId = tbody.attr('id'); 
    const fieldSlug = tbodyId.replace('attendanceTableBody-', '');
    const tbodyName = tbody.attr('name'); 

    // Ambil fieldName dari tbodyId
    const fieldName = tbodyId.replace('attendanceTableBody-', '').replace(/_/g, '[').replace(/$/, ']');
    // console.log('Field name for summary update:', tbodyName, tbodyId); 
    updateAttendanceSummary(fieldSlug,tbodyName);
});


// Update createFieldInput function untuk attendance
function createFieldInput(field, name, isTableCell = false) {
    const inputClass = isTableCell ? 'form-control form-control-sm' : 'form-control';
    const selectClass = isTableCell ? 'form-select form-select-sm' : 'form-select';
    const required = field.required ? 'required' : '';
    
    switch (field.type) {
        case 'attendance':
            return `
                <div class="attendance-container">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <label class="form-label mb-0">
                            <i class="fas fa-users me-2"></i>Daftar Kehadiran
                        </label>
                        <button type="button" class="btn btn-primary btn-sm" onclick="addAttendanceRow('${name.replace(/[^a-zA-Z0-9]/g, '_')}', '${name}')">
                            <i class="fas fa-plus"></i> Tambah Personnel
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="25%">Personnel</th>
                                    <th width="20%">Status</th>
                                    <th width="40%">Keterangan</th>
                                    <th width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceTableBody-${name.replace(/[^a-zA-Z0-9]/g, '_')}" name="${name}">
                                <!-- Rows akan ditambahkan via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                    
                    <input type="hidden" name="${name}" class="attendance-summary" value="">
                </div>
            `;
            
        // ... rest of existing cases
        case 'textarea':
            return `<textarea name="${name}" class="${inputClass}" rows="${isTableCell ? '1' : '3'}" placeholder="${field.default_value}" ${required}></textarea>`;
        
        case 'select':
            let selectHtml = `<select name="${name}" class="${selectClass}" ${required}>`;
            selectHtml += `<option value="">-- Pilih ${field.label} --</option>`;
            if (field.options) {
                field.options.forEach(option => {
                    selectHtml += `<option value="${option.value}">${option.label}</option>`;
                });
            }
            selectHtml += `</select>`;
            return selectHtml;
            
        default:
            return `<input type="text" name="${name}" class="${inputClass}" placeholder="${field.default_value}" ${required}>`;
    }
}
// Sub-report row counters
const subReportCounters = {};


// Sub-report designs data
const subReportDesigns = {
    @foreach($report->reportDesign->subDesigns as $subDesign)
    {{ $subDesign->id }}: {
        id: {{ $subDesign->id }},
        name: '{{ $subDesign->name }}',
        type: '{{ $subDesign->type }}',
        fields: [
            @foreach($subDesign->fields as $subField)
            {
                id: {{ $subField->id }},
                name: '{{ $subField->name }}',
                label: '{{ $subField->label }}',
                type: '{{ $subField->type }}',
                required: {{ $subField->required ? 'true' : 'false' }},
                default_value: '{{ $subField->default_value }}',
                options: @json($subField->options ?? [])
            },
            @endforeach
        ]
    },
    @endforeach
};

@foreach($report->reportDesign->subDesigns as $subDesign)
    subReportCounters[{{ $subDesign->id }}] = {{ $report->subData->where('report_sub_design_id', $subDesign->id)->count() }};
    var id = {{ $subDesign->id }};

    // addSubReportRow(id);
@endforeach

console.log('Sub Report Designs:', subReportDesigns);

function addSubReportRow(subDesignId) {
    const subDesign = subReportDesigns[subDesignId];
    const rowIndex = subReportCounters[subDesignId]++;
    
    if (subDesign.type === 'table') {
        addTableRow(subDesignId, subDesign, rowIndex);
    } else {
        addFormCard(subDesignId, subDesign, rowIndex);
    }
    // console.log('click', subDesignId, subDesign, rowIndex);
    updateSubReportCount(subDesignId);
}

function addTableRow(subDesignId, subDesign, rowIndex) {
    const tbody = document.getElementById(`subReportTableBody-${subDesignId}`);
    
    let rowHtml = `
        <tr id="subReportRow-${subDesignId}-${rowIndex}">
            <td>${rowIndex + 1}</td>
    `;
    
    subDesign.fields.forEach(field => {
        rowHtml += `<td>${createFieldInput(field, `sub_data[${subDesignId}][${rowIndex}][${field.name}]`, true)}</td>`;
    });
    
    rowHtml += `
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeSubReportRow(${subDesignId}, ${rowIndex})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
    
    tbody.insertAdjacentHTML('beforeend', rowHtml);
}

function addFormCard(subDesignId, subDesign, rowIndex) {
    const container = document.getElementById(`subReportCards-${subDesignId}`);
    
    let cardHtml = `
        <div class="card mb-3" id="subReportCard-${subDesignId}-${rowIndex}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">${subDesign.name} #${rowIndex + 1}</h6>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeSubReportRow(${subDesignId}, ${rowIndex})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="row">
    `;
    
    subDesign.fields.forEach(field => {
        cardHtml += `
            <div class="col-md-6 mb-3">
                <label class="form-label">
                    ${field.label}
                    ${field.required ? '<span class="text-danger">*</span>' : ''}
                </label>
                ${createFieldInput(field, `sub_data[${subDesignId}][${rowIndex}][${field.name}]`)}
            </div>
        `;
    });
    
    cardHtml += `
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', cardHtml);
}

function createFieldInput(field, name, isTableCell = false) {
    const inputClass = isTableCell ? 'form-control form-control-sm' : 'form-control';
    const selectClass = isTableCell ? 'form-select form-select-sm' : 'form-select';
    const required = field.required ? 'required' : '';
    
    switch (field.type) {
        case 'textarea':
            return `<textarea name="${name}" class="${inputClass}" rows="${isTableCell ? '1' : '3'}" placeholder="${field.default_value}" ${required}></textarea>`;
        
        case 'textarea_rich':
            return `<textarea name="${name}" class="${inputClass}" rows="4" placeholder="${field.default_value}" ${required}></textarea>`;
        
        case 'select':
            let selectHtml = `<select name="${name}" class="${selectClass}" ${required}>`;
            selectHtml += `<option value="">-- Pilih ${field.label} --</option>`;
            if (field.options) {
                field.options.forEach(option => {
                    selectHtml += `<option value="${option.value}">${option.label}</option>`;
                });
            }
            selectHtml += `</select>`;
            return selectHtml;
        
        case 'checkbox':
            return `
                <div class="form-check">
                    <input type="checkbox" name="${name}" value="1" class="form-check-input" ${field.default_value ? 'checked' : ''}>
                    <label class="form-check-label">${isTableCell ? 'Ya' : field.label}</label>
                </div>
            `;
        
        case 'file':
            return `<input type="file" name="${name}" class="${inputClass}" ${required}>`;
        
        case 'image':
            return `<input type="file" name="${name}" class="${inputClass}" accept="image/*" ${required}>`;
        
        case 'date':
            return `<input type="date" name="${name}" class="${inputClass}" value="${field.default_value}" ${required}>`;
        
        case 'time':
            return `<input type="time" name="${name}" class="${inputClass}" value="${field.default_value}" ${required}>`;
        
        case 'month':
            return `<input type="month" name="${name}" class="${inputClass}" value="${field.default_value}" ${required}>`;
        
        case 'year':
            return `<input type="number" name="${name}" class="${inputClass}" placeholder="YYYY" value="${field.default_value}" ${required}>`;
        
        case 'number':
            return `<input type="number" name="${name}" class="${inputClass}" placeholder="${field.default_value}" ${required}>`;
        
        case 'personnel':
            return `
                <select name="${name}" class="${selectClass}" ${required}>
                    <option value="">-- Pilih Personnel --</option>
                    @foreach($personnel ?? [] as $person)
                        <option value="{{ $person->id }}">{{ $person->name }}</option>
                    @endforeach
                </select>
            `;
        
        case 'map':
            return `
                <div class="border rounded p-3 bg-light text-center map-input" data-field="${name}">
                    <i class="fas fa-map-marker-alt fa-2x text-muted mb-2"></i>
                    <br>
                    <small class="text-muted">Klik untuk pilih lokasi</small>
                    <input type="hidden" name="${name}" class="map-coordinates">
                </div>
            `;
        
        case 'attendance':
            return `
                <div class="border rounded p-3 bg-light">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="openAttendanceModal('${name}')">
                        <i class="fas fa-users me-1"></i>Kelola Kehadiran
                    </button>
                    <input type="hidden" name="${name}" class="attendance-data">
                </div>
            `;
        
        default:
            return `<input type="text" name="${name}" class="${inputClass}" placeholder="${field.default_value}" ${required}>`;
    }
}

function removeSubReportRow(subDesignId, rowIndex) {
    const subDesign = subReportDesigns[subDesignId];
    
    if (subDesign.type === 'table') {
        const row = document.getElementById(`subReportRow-${subDesignId}-${rowIndex}`);
        if (row) row.remove();
    } else {
        const card = document.getElementById(`subReportCard-${subDesignId}-${rowIndex}`);
        if (card) card.remove();
    }
    
    updateSubReportCount(subDesignId);
    reorderRows(subDesignId);
}

function updateSubReportCount(subDesignId) {
    const subDesign = subReportDesigns[subDesignId];
    let count = 0;
    
    if (subDesign.type === 'table') {
        count = document.querySelectorAll(`#subReportTableBody-${subDesignId} tr`).length;
    } else {
        count = document.querySelectorAll(`#subReportCards-${subDesignId} .card`).length;
    }
    
    document.getElementById(`count-${subDesignId}`).textContent = count;
}

function reorderRows(subDesignId) {
    const subDesign = subReportDesigns[subDesignId];
    
    if (subDesign.type === 'table') {
        const rows = document.querySelectorAll(`#subReportTableBody-${subDesignId} tr`);
        rows.forEach((row, index) => {
            const numberCell = row.querySelector('td:first-child');
            if (numberCell) numberCell.textContent = index + 1;
        });
    } else {
        const cards = document.querySelectorAll(`#subReportCards-${subDesignId} .card`);
        cards.forEach((card, index) => {
            const header = card.querySelector('.card-header h6');
            if (header) {
                header.textContent = `${subDesign.name} #${index + 1}`;
            }
        });
    }
}

// Map functionality (basic implementation)
function initializeMapInputs() {
    document.querySelectorAll('.map-input').forEach(mapInput => {
        mapInput.addEventListener('click', function() {
            const fieldName = this.dataset.field;
            const hiddenInput = this.querySelector('.map-coordinates');
            
            // Simple implementation - you can integrate with Google Maps or other map services
            const lat = prompt('Masukkan Latitude:');
            const lng = prompt('Masukkan Longitude:');
            
            if (lat && lng) {
                hiddenInput.value = JSON.stringify({lat: parseFloat(lat), lng: parseFloat(lng)});
                this.innerHTML = `
                    <i class="fas fa-map-marker-alt text-success"></i>
                    <br>
                    <small class="text-success">Lokasi dipilih: ${lat}, ${lng}</small>
                `;
            }
        });
    });
}

// Attendance functionality (basic implementation)
function openAttendanceModal(fieldName) {
    // Simple implementation - you can create a proper attendance modal
    const attendanceData = prompt('Masukkan data kehadiran (JSON format):');
    
    if (attendanceData) {
        const hiddenInput = document.querySelector(`[name="${fieldName}"]`);
        hiddenInput.value = attendanceData;
        
        // Update UI
        const container = hiddenInput.closest('.border');
        container.innerHTML = `
            <i class="fas fa-users text-success"></i>
            <small class="text-success ms-2">Data kehadiran tersimpan</small>
            <button type="button" class="btn btn-outline-primary btn-sm ms-2" onclick="openAttendanceModal('${fieldName}')">
                Edit
            </button>
        `;
    }
}

// document.addEventListener('DOMContentLoaded', function() {
//     initializeMapInputs();
    
//     // Auto-save as draft every 2 minutes
//     setInterval(function() {
//         if (confirm('Auto-save draft?')) {
//             const form = document.getElementById('reportForm');
//             const formData = new FormData(form);
//             formData.set('action', 'draft');
            
//             fetch(form.action, {
//                 method: 'POST',
//                 body: formData,
//                 headers: {
//                     'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value
//                 }
//             }).then(response => {
//                 if (response.ok) {
//                     // Show success message
//                     const alert = document.createElement('div');
//                     alert.className = 'alert alert-success alert-dismissible fade show position-fixed';
//                     alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
//                     alert.innerHTML = `
//                         <i class="fas fa-save me-2"></i>Draft berhasil disimpan!
//                         <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
//                     `;
//                     document.body.appendChild(alert);
                    
//                     setTimeout(() => alert.remove(), 3000);
//                 }
//             });
//         }
//     }, 120000); // 2 minutes
// });

// Form validation before submit
document.getElementById('reportForm').addEventListener('submit', function(e) {
    const requiredFields = this.querySelectorAll('[required]');
    let hasErrors = false;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            hasErrors = true;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    if (hasErrors) {
        e.preventDefault();
        alert('Mohon lengkapi semua field yang wajib diisi');
    }
});
</script>

<style>
.map-input {
    cursor: pointer;
    min-height: 60px;
    transition: all 0.3s ease;
}

.map-input:hover {
    background-color: #e3f2fd !important;
    border-color: #2196f3 !important;
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
    font-size: 0.875rem;
}

.sub-report-card {
    border: 2px solid #dee2e6;
    transition: all 0.3s ease;
}

.sub-report-card:hover {
    border-color: #007bff;
    box-shadow: 0 4px 8px rgba(0,123,255,0.1);
}

.alert.position-fixed {
    max-width: 300px;
}

.form-control-sm, .form-select-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>

@endsection