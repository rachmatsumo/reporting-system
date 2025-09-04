@switch($field->type)
    @case('textarea')
    @case('textarea_rich')
        <div class="border rounded p-2 bg-light">
            {!! nl2br(e($value)) !!}
        </div>
        @break
        
    @case('select')
        @if($field->options)
            @php
                $selectedOption = collect($field->options)->firstWhere('value', $value);
            @endphp
            @if($selectedOption)
                <span class="badge bg-primary">{{ $selectedOption['label'] ?? $value }}</span>
            @else
                <span class="text-muted">{{ $value }}</span>
            @endif
        @else
            <span class="text-muted">{{ $value }}</span>
        @endif
        @break
        
    @case('checkbox')
        @if($value == '1' || $value === true)
            <span class="badge bg-success"><i class="fas fa-check"></i> Ya</span>
        @else
            <span class="badge bg-secondary"><i class="fas fa-times"></i> Tidak</span>
        @endif
        @break
        
    @case('file')
        @if($value)
            <a href="{{ Storage::url($value) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-download"></i> Download File
            </a>
        @else
            <span class="text-muted">Tidak ada file</span>
        @endif
        @break
        
    @case('image')
        @if($value)
            <div>
                <img src="{{ Storage::url($value) }}" alt="Image" class="img-thumbnail" 
                     style="max-width: {{ isset($compact) ? '100px' : '200px' }}; max-height: {{ isset($compact) ? '100px' : '200px' }};">
                <br>
                <a href="{{ Storage::url($value) }}" target="_blank" class="btn btn-outline-primary btn-sm mt-2">
                    <i class="fas fa-external-link-alt"></i> Lihat Penuh
                </a>
            </div>
        @else
            <span class="text-muted">Tidak ada gambar</span>
        @endif
        @break
        
    @case('date')
        @if($value)
            {{ \Carbon\Carbon::parse($value)->format('d/m/Y') }}
        @else
            <span class="text-muted">-</span>
        @endif
        @break
        
    @case('time')
        @if($value)
            {{ \Carbon\Carbon::parse($value)->format('H:i') }}
        @else
            <span class="text-muted">-</span>
        @endif
        @break
        
    @case('month')
        @if($value)
            {{ \Carbon\Carbon::parse($value)->format('F Y') }}
        @else
            <span class="text-muted">-</span>
        @endif
        @break
        
    @case('year')
        @if($value)
            <strong>{{ $value }}</strong>
        @else
            <span class="text-muted">-</span>
        @endif
        @break
        
    @case('number')
        @if($value !== null && $value !== '')
            <strong>{{ number_format($value, 0, ',', '.') }}</strong>
        @else
            <span class="text-muted">-</span>
        @endif
        @break
        
    @case('personnel')
        @if($value)
            @php
                $personnel = \App\Models\User::find($value);
            @endphp
            @if($personnel)
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm rounded-circle bg-primary text-white me-2">
                        {{ strtoupper(substr($personnel->name, 0, 2)) }}
                    </div>
                    <div>
                        <strong>{{ $personnel->name }}</strong>
                        @if($personnel->position)
                            <br><small class="text-muted">{{ $personnel->position }}</small>
                        @endif
                    </div>
                </div>
            @else
                <span class="text-muted">Personnel tidak ditemukan</span>
            @endif
        @else
            <span class="text-muted">-</span>
        @endif
        @break      
        
   @case('attendance')
        @if($value)
            @php
                $data = is_string($value) ? json_decode($value, true) : $value;
            @endphp

            @if(!empty($data['rows']))
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['rows'] as $row)
                            @php
                                $personnel = \App\Models\User::find($row['user_id']);
                            @endphp
                            <tr>
                                <td>{{ $row['user_id'] }}</td>
                                <td>{{ $personnel?->name ?? '-' }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $row['status'])) }}</td>
                                <td>{{ $row['note'] ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <span class="text-muted">Tidak ada data kehadiran</span>
            @endif
        @else
            <span class="text-muted">-</span>
        @endif
        @break
  

    @default
        <span>{{ $value }}</span>
@endswitch