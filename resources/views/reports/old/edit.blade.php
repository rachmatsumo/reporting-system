<!-- resources/views/admin/reports/edit.blade.php -->
@extends('layouts.user_type.auth')
 

@section('content')

<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h5 class="mb-0">Edit Report : {{ $report->reportDesign->name }}</h5>
                        <p class="mb-0 text-muted">{{ $report->reportDesign->description }}</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('reports.update', $report) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Judul Report <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                        value="{{ old('title', $report->title) }}">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr>

                            @foreach($report->reportDesign->fields as $field)
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">
                                            {{ ucwords($field->label) }}
                                            @if($field->required)
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>

                                        @php
                                            $fieldName = "data[{$field->name}]";
                                            $fieldValue = old("data.{$field->name}", $report->data[$field->name] ?? null);
                                            $hasError = $errors->has("data.{$field->name}");
                                            $errorClass = $hasError ? 'is-invalid' : '';
                                        @endphp

                                        @switch($field->type)
                                            @case('text')
                                                <input type="text" name="{{ $fieldName }}" 
                                                    class="form-control {{ $errorClass }}" 
                                                    value="{{ $fieldValue }}"
                                                    {{ $field->required ? 'required' : '' }}>
                                                @break

                                            @case('textarea')
                                                <textarea name="{{ $fieldName }}" 
                                                        class="form-control {{ $errorClass }}" 
                                                        rows="3" {{ $field->required ? 'required' : '' }}>{{ $fieldValue }}</textarea>
                                                @break

                                            @case('textarea_rich')
                                                <textarea name="{{ $fieldName }}" 
                                                        class="form-control richtext {{ $errorClass }}" 
                                                        rows="5" {{ $field->required ? 'required' : '' }}>{{ $fieldValue }}</textarea>
                                                @break

                                            @case('number')
                                                <input type="number" name="{{ $fieldName }}" 
                                                    class="form-control {{ $errorClass }}" 
                                                    value="{{ $fieldValue }}"
                                                    {{ $field->required ? 'required' : '' }}>
                                                @break

                                            @case('date')
                                                <input type="date" name="{{ $fieldName }}" 
                                                    class="form-control {{ $errorClass }}" 
                                                    value="{{ $fieldValue }}"
                                                    {{ $field->required ? 'required' : '' }}>
                                                @break

                                            @case('time')
                                                <input type="time" name="{{ $fieldName }}" 
                                                    class="form-control {{ $errorClass }}" 
                                                    value="{{ $fieldValue }}"
                                                    {{ $field->required ? 'required' : '' }}>
                                                @break

                                            @case('month')
                                                <input type="month" name="{{ $fieldName }}" 
                                                    class="form-control {{ $errorClass }}" 
                                                    value="{{ $fieldValue }}"
                                                    {{ $field->required ? 'required' : '' }}>
                                                @break

                                            @case('year')
                                                <input type="number" name="{{ $fieldName }}" 
                                                    class="form-control {{ $errorClass }}" 
                                                    min="1900" max="2100" step="1"
                                                    value="{{ $fieldValue }}"
                                                    {{ $field->required ? 'required' : '' }}>
                                                @break

                                            @case('file')
                                            @case('image')
                                                @if($fieldValue)
                                                    <div class="mb-2">
                                                        <small class="text-muted">File saat ini:</small>
                                                        @if($field->type === 'image')
                                                            <br><img src="{{ Storage::url($fieldValue) }}" 
                                                                    alt="Current image" style="max-height: 100px;">
                                                        @else
                                                            <br><a href="{{ Storage::url($fieldValue) }}" target="_blank">
                                                                {{ basename($fieldValue) }}
                                                            </a>
                                                        @endif
                                                        <br><small class="text-info">Kosongkan jika tidak ingin mengubah file</small>
                                                    </div>
                                                @endif
                                                <input type="file" name="{{ $fieldName }}" 
                                                    class="form-control {{ $errorClass }}" 
                                                    {{ $field->type === 'image' ? 'accept="image/*"' : '' }}>
                                                @break

                                            @case('checkbox')
                                                <div class="form-check">
                                                    <input type="checkbox" name="{{ $fieldName }}" value="1" 
                                                        class="form-check-input {{ $errorClass }}" 
                                                        id="checkbox-{{ $field->id }}"
                                                        {{ $fieldValue ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="checkbox-{{ $field->id }}">
                                                        Ya
                                                    </label>
                                                </div>
                                                @break

                                            @case('select')
                                                <select name="{{ $fieldName }}" 
                                                        class="form-select {{ $errorClass }}" 
                                                        {{ $field->required ? 'required' : '' }}>
                                                    @if(!$field->required)
                                                        <option value="">Pilih...</option>
                                                    @endif
                                                    @php
                                                        $options = json_decode($field->default_value, true) ?? [];
                                                    @endphp
                                                    @foreach($options as $option)
                                                        <option value="{{ $option['value'] }}" 
                                                                {{ $fieldValue === $option['value'] ? 'selected' : '' }}>
                                                            {{ $option['label'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @break
                                                
                                            @case('personnel')
                                                @php
                                                    $options = json_decode($field->default_value, true) ?? []; 
                                                @endphp

                                                <div class="mb-3">
                                                    {{-- <label class="form-label">Pilih Personnel</label> --}}
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>ID</th>
                                                                <th>Name</th>
                                                                <th>Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="personnel-rows">
                                                        </tbody>
                                                    </table>
                                                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-personnel">
                                                        + Tambah Personnel
                                                    </button>
                                                </div>
                                                @break

                                            @case('attendance')
                                                    @php
                                                    $options = json_decode($field->default_value, true) ?? []; 
                                                @endphp

                                                <div class="mb-3">
                                                    {{-- <label class="form-label">Attendance</label> --}}
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>ID</th>
                                                                <th>Name</th>
                                                                <th>Status</th>
                                                                <th>Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="attendance-rows">
                                                        </tbody>
                                                    </table>
                                                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-attendance">
                                                        + Tambah Attendance
                                                    </button>
                                                </div> 
                                                @break
                                        @endswitch

                                        @if($hasError)
                                            <div class="invalid-feedback">
                                                {{ $errors->first("data.{$field->name}") }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            <hr>

                            <div class="text-end mt-5">
                                <a href="{{ route('reports.show', $report) }}" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Update Report</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</main> 

@push('scripts')
<script>
    console.log('Edit script loaded!');
    
    const users = @json($users ?? []);
    const reportData = @json($report->data ?? []);
    const fields = @json($report->reportDesign->fields ?? []);
    
    console.log('Users:', users);
    console.log('Report data:', reportData);
    console.log('Fields:', fields);

    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM ready for edit page');
        
        // Inisialisasi data yang sudah ada untuk setiap field
        fields.forEach(field => {
            if (field.type === 'personnel') {
                initializePersonnelData(field.name);
            } else if (field.type === 'attendance') {
                initializeAttendanceData(field.name);
            }
        });
        
        // Setup event handlers
        setupEventHandlers();
    });

    function setupEventHandlers() {
        // Personnel event handler
        const addPersonnelBtn = document.getElementById('add-personnel');
        if (addPersonnelBtn) {
            addPersonnelBtn.addEventListener('click', function() {
                console.log('Add personnel clicked');
                // Cari field personnel
                const personnelField = fields.find(f => f.type === 'personnel');
                if (personnelField) {
                    addPersonnelRow(personnelField.name);
                }
            });
        }

        // Attendance event handler  
        const addAttendanceBtn = document.getElementById('add-attendance');
        if (addAttendanceBtn) {
            addAttendanceBtn.addEventListener('click', function() {
                console.log('Add attendance clicked');
                // Cari field attendance
                const attendanceField = fields.find(f => f.type === 'attendance');
                if (attendanceField) {
                    addAttendanceRow(attendanceField.name);
                }
            });
        }
    }

    function initializePersonnelData(fieldName) {
        console.log('Initializing personnel data for field:', fieldName);
        const existingData = reportData[fieldName];
        
        if (existingData && Array.isArray(existingData)) {
            existingData.forEach((userId, index) => {
                if (userId) { // Skip empty values
                    addPersonnelRow(fieldName, userId, `existing_${index}`);
                }
            });
        }
    }

    function initializeAttendanceData(fieldName) {
        console.log('Initializing attendance data for field:', fieldName);
        const existingData = reportData[fieldName];
        
        if (existingData && typeof existingData === 'object') {
            Object.entries(existingData).forEach(([key, value]) => {
                if (value && value.user_id) { // Skip empty values
                    addAttendanceRow(fieldName, value.user_id, value.status, key);
                }
            });
        }
    }

    function addPersonnelRow(fieldName, selectedUserId = null, customId = null) {
        const rowId = customId || Date.now();
        const personnelRows = document.getElementById('personnel-rows');
        
        if (!personnelRows) {
            console.error('Personnel rows container not found');
            return;
        }

        if (!users || users.length === 0) {
            console.error('No users data available');
            return;
        }

        let userOptions = users.map(u => {
            const selected = selectedUserId && u.id == selectedUserId ? 'selected' : '';
            return `<option value="${u.id}" ${selected}>${u.id} - ${u.name}</option>`;
        }).join('');

        // Get user name for display
        const selectedUser = selectedUserId ? users.find(u => u.id == selectedUserId) : null;
        const displayName = selectedUser ? selectedUser.name : '-';

        let row = `
            <tr id="personnel-${rowId}">
                <td>
                    <select name="data[${fieldName}][${rowId}]" class="form-select">
                        <option value="">Pilih Personnel...</option>
                        ${userOptions}
                    </select>
                </td>
                <td>${displayName}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeRow('personnel-${rowId}')">Hapus</button>
                </td>
            </tr>
        `;
        
        personnelRows.insertAdjacentHTML('beforeend', row);
        console.log('Personnel row added:', rowId, 'for field:', fieldName);
    }

    function addAttendanceRow(fieldName, selectedUserId = null, selectedStatus = 'Present', customId = null) {
        const rowId = customId || Date.now();
        const attendanceRows = document.getElementById('attendance-rows');
        
        if (!attendanceRows) {
            console.error('Attendance rows container not found');
            return;
        }

        if (!users || users.length === 0) {
            console.error('No users data available');
            return;
        }

        let userOptions = users.map(u => {
            const selected = selectedUserId && u.id == selectedUserId ? 'selected' : '';
            return `<option value="${u.id}" ${selected}>${u.id} - ${u.name}</option>`;
        }).join('');

        let statusOptions = ['Present', 'Absent', 'Leave', 'Permit'].map(status => {
            const selected = status === selectedStatus ? 'selected' : '';
            return `<option value="${status}" ${selected}>${status}</option>`;
        }).join('');

        // Get user name for display
        const selectedUser = selectedUserId ? users.find(u => u.id == selectedUserId) : null;
        const displayName = selectedUser ? selectedUser.name : '-';

        let row = `
            <tr id="attendance-${rowId}">
                <td>
                    <select name="data[${fieldName}][${rowId}][user_id]" class="form-select">
                        <option value="">Pilih Personnel...</option>
                        ${userOptions}
                    </select>
                </td>
                <td>${displayName}</td>
                <td>
                    <select name="data[${fieldName}][${rowId}][status]" class="form-select">
                        ${statusOptions}
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeRow('attendance-${rowId}')">Hapus</button>
                </td>
            </tr>
        `;
        
        attendanceRows.insertAdjacentHTML('beforeend', row);
        console.log('Attendance row added:', rowId, 'for field:', fieldName);
    }

    // Global function untuk remove row
    function removeRow(id) {
        console.log('Removing row:', id);
        const element = document.getElementById(id);
        if (element) {
            element.remove();
            console.log('Row removed successfully');
        } else {
            console.error('Row not found:', id);
        }
    }
</script>
@endpush

@endsection