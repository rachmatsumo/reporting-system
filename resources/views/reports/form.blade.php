@extends('layouts.user_type.auth') 

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>New Report : {{ $reportDesign->name }}</h5>
                        <p class="mb-0 text-muted">{{ $reportDesign->description }}</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="report_design_id" value="{{ $reportDesign->id }}">
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Judul Report <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                        value="{{ old('title') }}">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr>

                            @foreach($reportDesign->fields as $field)
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
                                            $fieldValue = old("data.{$field->name}");
                                            $hasError = $errors->has("data.{$field->name}");
                                            $errorClass = $hasError ? 'is-invalid' : '';
                                        @endphp

                                        @switch($field->type)
                                            @case('text')
                                                <input type="text" name="{{ $fieldName }}" 
                                                    class="form-control {{ $errorClass }}" 
                                                    value="{{ $fieldValue ?? $field->default_value }}"
                                                    {{ $field->required ? 'required' : '' }}>
                                                @break

                                            @case('textarea')
                                                <textarea name="{{ $fieldName }}" 
                                                        class="form-control {{ $errorClass }}" 
                                                        rows="3" {{ $field->required ? 'required' : '' }}>{{ $fieldValue ?? $field->default_value }}</textarea>
                                                @break

                                            @case('textarea_rich')
                                                <textarea name="{{ $fieldName }}" 
                                                        class="form-control richtext {{ $errorClass }}" 
                                                        rows="5" {{ $field->required ? 'required' : '' }}>{{ $fieldValue ?? $field->default_value }}</textarea>
                                                @break

                                            @case('number')
                                                <input type="number" name="{{ $fieldName }}" 
                                                    class="form-control {{ $errorClass }}" 
                                                    value="{{ $fieldValue ?? $field->default_value }}"
                                                    {{ $field->required ? 'required' : '' }}>
                                                @break

                                            @case('date')
                                                <input type="date" name="{{ $fieldName }}" 
                                                    class="form-control {{ $errorClass }}" 
                                                    value="{{ $fieldValue ?? $field->default_value }}"
                                                    {{ $field->required ? 'required' : '' }}>
                                                @break

                                            @case('time')
                                                <input type="time" name="{{ $fieldName }}" 
                                                    class="form-control {{ $errorClass }}" 
                                                    value="{{ $fieldValue ?? $field->default_value }}"
                                                    {{ $field->required ? 'required' : '' }}>
                                                @break

                                            @case('month')
                                                <input type="month" name="{{ $fieldName }}" 
                                                    class="form-control {{ $errorClass }}" 
                                                    value="{{ $fieldValue ?? $field->default_value }}"
                                                    {{ $field->required ? 'required' : '' }}>
                                                @break

                                            @case('year')
                                                <input type="number" name="{{ $fieldName }}" 
                                                    class="form-control {{ $errorClass }}" 
                                                    min="1900" max="2100" step="1"
                                                    value="{{ $fieldValue ?? $field->default_value }}"
                                                    {{ $field->required ? 'required' : '' }}>
                                                @break

                                            @case('file')
                                            @case('image')
                                                <input type="file" name="{{ $fieldName }}" 
                                                    class="form-control {{ $errorClass }}" 
                                                    {{ $field->type === 'image' ? 'accept="image/*"' : '' }}
                                                    {{ $field->required ? 'required' : '' }}>
                                                @if($field->type === 'image')
                                                    <small class="form-text text-muted">Format: JPEG, PNG, JPG, GIF. Maksimal 2MB</small>
                                                @endif
                                                @break

                                            @case('checkbox')
                                                <div class="form-check">
                                                    <input type="checkbox" name="{{ $fieldName }}" value="1" 
                                                        class="form-check-input {{ $errorClass }}" 
                                                        id="checkbox-{{ $field->id }}"
                                                        {{ ($fieldValue || $field->default_value) ? 'checked' : '' }}>
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
                                <a href="{{ route('reports.create') }}" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Simpan Report</button>
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
    console.log('Create script loaded!');
    
    const users = @json($users ?? []);
    const fields = @json($reportDesign->fields ?? []);
    
    console.log('Users:', users);
    console.log('Fields:', fields);

    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM ready for create page');
        
        // Setup event handlers
        setupEventHandlers();
    });

    function setupEventHandlers() {
        // Personnel event handler
        const addPersonnelBtn = document.getElementById('add-personnel');
        if (addPersonnelBtn) {
            console.log('Personnel button found');
            addPersonnelBtn.addEventListener('click', function() {
                console.log('Add personnel clicked');
                // Cari field personnel
                const personnelField = fields.find(f => f.type === 'personnel');
                if (personnelField) {
                    addPersonnelRow(personnelField.name);
                } else {
                    console.error('Personnel field not found');
                }
            });
        }

        // Attendance event handler  
        const addAttendanceBtn = document.getElementById('add-attendance');
        if (addAttendanceBtn) {
            console.log('Attendance button found');
            addAttendanceBtn.addEventListener('click', function() {
                console.log('Add attendance clicked');
                // Cari field attendance
                const attendanceField = fields.find(f => f.type === 'attendance');
                if (attendanceField) {
                    addAttendanceRow(attendanceField.name);
                } else {
                    console.error('Attendance field not found');
                }
            });
        }
    }

    function addPersonnelRow(fieldName) {
        const rowId = Date.now();
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
            return `<option value="${u.id}">${u.id} - ${u.name}</option>`;
        }).join('');

        let row = `
            <tr id="personnel-${rowId}">
                <td>
                    <select name="data[${fieldName}][${rowId}]" class="form-select">
                        <option value="">Pilih Personnel...</option>
                        ${userOptions}
                    </select>
                </td>
                <td>-</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeRow('personnel-${rowId}')">Hapus</button>
                </td>
            </tr>
        `;
        
        personnelRows.insertAdjacentHTML('beforeend', row);
        console.log('Personnel row added:', rowId, 'for field:', fieldName);
    }

    function addAttendanceRow(fieldName) {
        const rowId = Date.now();
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
            return `<option value="${u.id}">${u.id} - ${u.name}</option>`;
        }).join('');

        let statusOptions = ['Present', 'Absent', 'Leave', 'Permit'].map(status => {
            return `<option value="${status}">${status}</option>`;
        }).join('');

        let row = `
            <tr id="attendance-${rowId}">
                <td>
                    <select name="data[${fieldName}][${rowId}][user_id]" class="form-select">
                        <option value="">Pilih Personnel...</option>
                        ${userOptions}
                    </select>
                </td>
                <td>-</td>
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