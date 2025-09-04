{{-- resources/views/report-design/partials/field-preview.blade.php --}}

@php
    $inputClass = isset($small) && $small ? 'form-control form-control-sm' : 'form-control';
    $selectClass = isset($small) && $small ? 'form-select form-select-sm' : 'form-select';
@endphp

@switch($field->type)
    @case('textarea')
        <textarea class="{{ $inputClass }}" rows="{{ isset($small) ? '2' : '3' }}" 
                  placeholder="{{ $field->default_value }}" disabled></textarea>
        @break
        
    @case('textarea_rich')
        <div class="border rounded p-2 bg-light">
            <small class="text-muted">
                <i class="fas fa-edit me-1"></i>Rich Text Editor
                @if($field->default_value)
                    - Default: {{ Str::limit($field->default_value, 30) }}
                @endif
            </small>
        </div>
        @break
        
    @case('select')
        <select class="{{ $selectClass }}" disabled>
            <option>-- Pilih {{ $field->label }} --</option>
            @if($field->options)
                @foreach($field->options as $option)
                    <option value="{{ $option['value'] ?? '' }}">
                        {{ $option['label'] ?? $option['value'] ?? '' }}
                    </option>
                @endforeach
            @endif
        </select>
        @break
        
    @case('checkbox')
        <div class="form-check">
            <input type="checkbox" class="form-check-input" disabled 
                   {{ $field->default_value ? 'checked' : '' }}>
            <label class="form-check-label">
                @if(isset($small) && $small)
                    Checked
                @else
                    {{ $field->label }}
                @endif
            </label>
        </div>
        @break
        
    @case('file')
        <input type="file" class="{{ $inputClass }}" disabled>
        <small class="text-muted">File upload field</small>
        @break
        
    @case('image')
        <input type="file" class="{{ $inputClass }}" accept="image/*" disabled>
        <small class="text-muted">Image upload field</small>
        @break
        
    @case('map')
        <div class="border rounded p-3 bg-light text-center">
            <i class="fas fa-map-marker-alt fa-2x text-muted mb-2"></i>
            <br>
            <small class="text-muted">Map Location Picker</small>
        </div>
        @break
        
    @case('personnel')
        <select class="{{ $selectClass }}" disabled>
            <option>-- Pilih Personnel --</option>
            <option>John Doe</option>
            <option>Jane Smith</option>
        </select>
        <small class="text-muted">Personnel selection field</small>
        @break
        
    @case('attendance')
        <div class="border rounded p-2 bg-light">
            <small class="text-muted">
                <i class="fas fa-users me-1"></i>Attendance tracking component
            </small>
        </div>
        @break
        
    @case('date')
        <input type="date" class="{{ $inputClass }}" 
               value="{{ $field->default_value }}" disabled>
        @break
        
    @case('time')
        <input type="time" class="{{ $inputClass }}" 
               value="{{ $field->default_value }}" disabled>
        @break
        
    @case('month')
        <input type="month" class="{{ $inputClass }}" 
               value="{{ $field->default_value }}" disabled>
        @break
        
    @case('year')
        <input type="number" class="{{ $inputClass }}" 
               placeholder="YYYY" value="{{ $field->default_value }}" disabled>
        @break
        
    @case('number')
        <input type="number" class="{{ $inputClass }}" 
               placeholder="{{ $field->default_value }}" disabled>
        @break
        
    @default
        <input type="text" class="{{ $inputClass }}" 
               placeholder="{{ $field->default_value }}" disabled>
@endswitch