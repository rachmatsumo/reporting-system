@switch($field->type)
    @case('textarea')
    @case('textarea_rich')
        <div style="border:1px solid #ccc; padding:5px; margin-top:4px;">{!! $value !!}</div>
        @break

    @case('select')
        @if($field->options)
            @php
                $opt = collect($field->options)->firstWhere('value', $value);
            @endphp
            {{ $opt['label'] ?? $value }}
        @else
            {{ $value ?: '-' }}
        @endif
        @break

    @case('checkbox')
        {{ ($value == '1' || $value === true) ? 'Ya' : 'Tidak' }}
        @break

    @case('file')
        @if($value)
            <a href="{{ asset($value) }}" target="_blank">Unduh file</a>
        @else
            <span class="text-muted">Tidak ada file</span>
        @endif
        @break

    @case('image')
        @if($value)
            <img src="{{ public_path($value) }}" alt="Image">
        @else
            <span class="text-muted">Tidak ada gambar</span>
        @endif
        @break

    @case('signing')
        @if($value)
            <img src="{{ $value }}" alt="Signature" style="max-width:200px;margin-top :10px">
        @endif
        @break

    @case('personnel')
        @php
            $person = \App\Models\User::find($value);
        @endphp
        @if($person)
            {{ $person->name }}
            @if($person->position)
                <small>({{ $person->position }})</small>
            @endif
        @else
            <span class="text-muted">Personnel tidak ditemukan</span>
        @endif
        @break

    @case('attendance')
        @php
            $data = is_string($value) ? json_decode($value, true) : $value;
        @endphp
        @if(!empty($data['rows']))
            <table>
                <thead>
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
                            $person = \App\Models\User::find($row['user_id']);
                        @endphp
                        <tr>
                            <td>{{ $row['user_id'] }}</td>
                            <td>{{ $person?->name ?? '-' }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $row['status'])) }}</td>
                            <td>{{ $row['note'] ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <span class="text-muted">Tidak ada data kehadiran</span>
        @endif
        @break

   @case('map')
    @if($value)
        @php 
            $coordinates = is_string($value) ? json_decode($value, true) : $value;
        @endphp
        @if(isset($coordinates['lat']) && isset($coordinates['lng']))
            <div style="border-left:4px solid #4285f4; padding:12px; background:#f8f9fa; margin-top:6px;">
                <div style="margin-bottom:8px;">
                    <strong style="color:#202124; font-size:12px;">📍 Koordinat Lokasi</strong>
                </div>
                <div style="background:white; padding:8px; border-radius:4px; margin-bottom:8px;">
                    <table style="border:none; width:100%;">
                        <tr style="border:none;">
                            <td style="border:none; padding:3px 0; color:#5f6368; font-size:10px; width:70px;">Latitude:</td>
                            <td style="border:none; padding:3px 0; font-family:monospace; font-size:11px; color:#202124;">
                                {{ number_format($coordinates['lat'], 7) }}
                            </td>
                        </tr>
                        <tr style="border:none;">
                            <td style="border:none; padding:3px 0; color:#5f6368; font-size:10px;">Longitude:</td>
                            <td style="border:none; padding:3px 0; font-family:monospace; font-size:11px; color:#202124;">
                                {{ number_format($coordinates['lng'], 7) }}
                            </td>
                        </tr>
                    </table>
                </div>
                <div style="font-size:10px;">
                    <a href="https://www.google.com/maps?q={{ $coordinates['lat'] }},{{ $coordinates['lng'] }}" 
                       target="_blank" 
                       style="color:#1967d2; text-decoration:none; margin-right:10px;">
                        → Lihat di Google Maps
                    </a>
                    {{-- <a href="https://www.openstreetmap.org/?mlat={{ $coordinates['lat'] }}&mlon={{ $coordinates['lng'] }}&zoom=16" 
                       target="_blank" 
                       style="color:#7ebc6f; text-decoration:none;">
                        → Lihat di OSM
                    </a> --}}
                </div>
            </div>
        @else
            <span class="text-muted">Format koordinat tidak valid</span>
        @endif
    @else
        <span class="text-muted">Lokasi tidak diset</span>
    @endif
    @break

    @case('number')
        {{ number_format($value ?? 0, 0, ',', '.') }}
        @break

    @case('date')
        {{ $value ? \Carbon\Carbon::parse($value)->format('d/m/Y') : '-' }}
        @break

    @case('time')
        {{ $value ? \Carbon\Carbon::parse($value)->format('H:i') : '-' }}
        @break

    @default
        {{ $value ?? '-' }}
@endswitch
