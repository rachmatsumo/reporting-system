{{-- resources/views/reports/partials/field-input.blade.php --}}

@php
    $inputClass = 'form-control' . (isset($errors) && $errors->has($name) ? ' is-invalid' : '');
    $selectClass = 'form-select' . (isset($errors) && $errors->has($name) ? ' is-invalid' : '');
    $required = $field->required ? 'required' : '';
@endphp

@switch($field->type)
    @case('textarea')
        <textarea name="{{ $name }}" class="{{ $inputClass }}" rows="3" 
                  placeholder="{{ $field->default_value }}" {{ $required }}>{{ $value }}</textarea>
        @break
        
    @case('textarea_rich')
        <textarea name="{{ $name }}" class="{{ $inputClass }} rich-editor richtext" rows="4" 
                  placeholder="{{ $field->default_value }}" {{ $required }}>{{ $value }}</textarea>
        {{-- <small class="text-muted">Rich text editor akan aktif di sini</small> --}}
        @break
        
    @case('select')
        <select name="{{ $name }}" class="{{ $selectClass }}" {{ $required }}>
            <option value="">-- Pilih {{ $field->label }} --</option>
            @if($field->options)
                @foreach($field->options as $option)
                    <option value="{{ $option['value'] ?? '' }}" 
                            {{ ($value == ($option['value'] ?? '')) ? 'selected' : '' }}>
                        {{ $option['label'] ?? $option['value'] ?? '' }}
                    </option>
                @endforeach
            @endif
        </select>
        @break
        
    @case('checkbox')
        <div class="form-check">
            <input type="hidden" name="{{ $name }}" value="0">
            <input type="checkbox" name="{{ $name }}" value="1" 
                   class="form-check-input {{ isset($errors) && $errors->has($name) ? 'is-invalid' : '' }}" 
                   id="{{ Str::slug($name) }}" 
                   {{ ($value == '1' || $value === true || $field->default_value) ? 'checked' : '' }}>
            <label class="form-check-label" for="{{ Str::slug($name) }}">
                {{ $field->label }}
            </label>
        </div>
        @break
        
    @case('file')
        <input type="file" name="{{ $name }}" class="{{ $inputClass }}" {{ $required }}>
        @if($field->default_value)
            <small class="text-muted">Format yang diizinkan: {{ $field->default_value }}</small>
        @endif
        @break
        
    @case('image')
        <input type="file" name="{{ $name }}" class="{{ $inputClass }}" accept="image/*" {{ $required }}>
        <small class="text-muted">Format gambar: JPG, PNG, GIF (Max: 2MB)</small>
        @break
        
    @case('date')
        <input type="date" name="{{ $name }}" class="{{ $inputClass }}" 
               value="{{ $value ?: $field->default_value }}" {{ $required }}>
        @break
        
    @case('time')
        <input type="time" name="{{ $name }}" class="{{ $inputClass }}" 
               value="{{ $value ?: $field->default_value }}" {{ $required }}>
        @break
        
    @case('month')
        <input type="month" name="{{ $name }}" class="{{ $inputClass }}" 
               value="{{ $value ?: $field->default_value }}" {{ $required }}>
        @break
        
    @case('year')
        <input type="number" name="{{ $name }}" class="{{ $inputClass }}" 
               placeholder="YYYY" min="1900" max="2100"
               value="{{ $value ?: $field->default_value }}" {{ $required }}>
        @break
        
    @case('number')
        <input type="number" name="{{ $name }}" class="{{ $inputClass }}" 
               placeholder="{{ $field->default_value }}" 
               value="{{ $value }}" {{ $required }}>
        @break
        
    @case('personnel')
        <select name="{{ $name }}" class="{{ $selectClass }}" {{ $required }}>
            <option value="">-- Pilih Personnel --</option>
            @foreach($personnel ?? [] as $person)
                <option value="{{ $person->id }}" {{ $value == $person->id ? 'selected' : '' }}>
                    {{ $person->name }} - {{ $person->position ?? '' }}
                </option>
            @endforeach
        </select>
        @break

    @case('map')
        <div class="map-input-container">
            <div class="border rounded p-3 bg-light text-center map-input" 
                data-field="{{ $name }}" onclick="openMapSelector('{{ $name }}')">
                @if($value)
                    @php $coordinates = is_string($value) ? json_decode($value, true) : $value; @endphp
                    @if(isset($coordinates['lat']) && isset($coordinates['lng']))
                        <i class="bi bi-map text-success fa-2x mb-2"></i>
                        <br>
                        <small class="text-success">
                            Location: {{ number_format($coordinates['lat'], 4) }}, {{ number_format($coordinates['lng'], 4) }}
                        </small>
                        <br>
                        <small class="text-muted">Klik untuk mengubah lokasi</small>
                    @else
                        <i class="bi bi-map fa-2x text-muted mb-2"></i>
                        <br>
                        <small class="text-muted">Klik untuk pilih lokasi</small>
                    @endif
                @else
                    <i class="bi bi-map fa-2x text-muted mb-2"></i>
                    <br>
                    <small class="text-muted">Klik untuk pilih lokasi</small>
                @endif
            </div>
            <input type="hidden" name="{{ $name }}" class="map-coordinates" value="{{ $value }}">
        </div>
        @break
        
    {{-- @case('map')
        <div class="map-input-container">
            <div class="border rounded p-3 bg-light text-center map-input" 
                 data-field="{{ $name }}" onclick="openMapSelector('{{ $name }}')">
                @if($value)
                    @php $coordinates = is_string($value) ? json_decode($value, true) : $value; @endphp
                    @if(isset($coordinates['lat']) && isset($coordinates['lng']))
                        <i class="bi bi-map text-success fa-2x mb-2"></i>
                        <br>
                        <small class="text-success">
                            Lokasi: {{ $coordinates['lat'] }}, {{ $coordinates['lng'] }}
                        </small>
                    @else
                        <i class="bi bi-map fa-2x text-muted mb-2"></i>
                        <br>
                        <small class="text-muted">Klik untuk pilih lokasi</small>
                    @endif
                @else
                    <i class="bi bi-map fa-2x text-muted mb-2"></i>
                    <br>
                    <small class="text-muted">Klik untuk pilih lokasi</small>
                @endif
            </div>
            <input type="hidden" name="{{ $name }}" class="map-coordinates" value="{{ $value }}">
        </div>
        @break --}}
        
    @case('attendance')
        <div class="attendance-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <label class="form-label mb-0">
                    <i class="bi bi-people me-2"></i>Daftar Kehadiran
                </label>
                <button type="button" class="btn btn-primary btn-sm" name="{{ $name }}"
                        onclick="addAttendanceRow('{{ Str::slug($name) }}', '{{ $name }}')">
                    <i class="bi bi-plus"></i> Tambah Personnel
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
                    <tbody id="attendanceTableBody-{{ Str::slug($name) }}" name="{{ $name }}">
                        @if($value)
                            @php 
                                $attendanceData = is_string($value) ? json_decode($value, true) : ($value ?? []);
                                $rows = $attendanceData['rows'] ?? [];
                            @endphp
                            @foreach($rows as $index => $row)
                                <tr id="attendanceRow-{{ Str::slug($name) }}-{{ $index }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <select name="attendance[{{ $name }}][{{ $index }}][user_id]" class="form-select form-select-sm" required>
                                            <option value="">-- Pilih Personnel --</option>
                                            @foreach($users ?? [] as $user)
                                                <option value="{{ $user->id }}" {{ ($row['user_id'] ?? '') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="attendance[{{ $name }}][{{ $index }}][status]" class="form-select form-select-sm" required>
                                            <option value="">-- Pilih Status --</option>
                                            <option value="hadir" {{ ($row['status'] ?? '') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                            <option value="tidak_hadir" {{ ($row['status'] ?? '') == 'tidak_hadir' ? 'selected' : '' }}>Tidak Hadir</option>
                                            <option value="izin" {{ ($row['status'] ?? '') == 'izin' ? 'selected' : '' }}>Izin</option>
                                            <option value="sakit" {{ ($row['status'] ?? '') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="attendance[{{ $name }}][{{ $index }}][note]" 
                                            class="form-control form-control-sm" 
                                            placeholder="Keterangan..." 
                                            value="{{ $row['note'] ?? '' }}">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm" 
                                                onclick="removeAttendanceRow('{{ Str::slug($name) }}', {{ $index }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            
            <input type="hidden" name="{{ $name }}" class="attendance-summary" value="{{ $value }}">
        </div>
        @break
        
    @default
        <input type="text" name="{{ $name }}" class="{{ $inputClass }}" 
               placeholder="{{ $field->default_value }}" 
               value="{{ $value }}" {{ $required }}>
@endswitch

@if($field->default_value && !in_array($field->type, ['checkbox', 'map', 'attendance']))
    <small class="text-muted">Default: {{ $field->default_value }}</small>
@endif