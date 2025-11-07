@switch($field->type)
    @case('textarea')
    @case('textarea_rich')
        <div class="border rounded p-2 bg-light text-wrap ">
            {!! $value !!}
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
            <span class="badge bg-success"><i class="bi bi-check"></i> Ya</span>
        @else
            <span class="badge bg-secondary"><i class="bi bi-x"></i> Tidak</span>
        @endif
        @break

    {{-- @case('map')
        @if($value)
            @php 
                $coordinates = is_string($value) ? json_decode($value, true) : $value;
            @endphp
            @if(isset($coordinates['lat']) && isset($coordinates['lng']))
                <div class="d-flex align-items-center">
                    <i class="bi bi-map text-danger me-2"></i>
                    <div>
                        <strong>{{ number_format($coordinates['lat'], 6) }}, {{ number_format($coordinates['lng'], 6) }}</strong>
                        <br>
                        <small class="text-muted">
                            <a href="https://www.google.com/maps?q={{ $coordinates['lat'] }},{{ $coordinates['lng'] }}" 
                            target="_blank" class="text-decoration-none">
                                <i class="bi bi-link me-1"></i>Lihat di Google Maps
                            </a>
                        </small>
                    </div>
                </div>
            @else
                <span class="text-muted">Format koordinat tidak valid</span>
            @endif
        @else
            <span class="text-muted">Lokasi tidak diset</span>
        @endif
        @break --}}

    @case('map')
        @if($value)
            @php 
                $coordinates = is_string($value) ? json_decode($value, true) : $value;
                $mapId = 'map_' . uniqid();
            @endphp
            @if(isset($coordinates['lat']) && isset($coordinates['lng']))
                <div class="map-display-container">
                    <div id="{{ $mapId }}" style="height: 200px; width: 100%; border-radius: 8px; border: 1px solid #dee2e6;"></div>
                    <div class="mt-2 d-flex flex-column">
                        <small class="text-muted">
                            <i class="bi bi-map me-1"></i>
                            {{ number_format($coordinates['lat'], 6) }}, {{ number_format($coordinates['lng'], 6) }}
                        </small>
                         <a href="https://www.google.com/maps?q={{ $coordinates['lat'] }},{{ $coordinates['lng'] }}" 
                            target="_blank" class="text-decoration-none">
                            <i class="bi bi-link me-1"></i>Lihat di Google Maps
                        </a>
                        {{-- <a href="https://www.google.com/maps?q={{ $coordinates['lat'] }},{{ $coordinates['lng'] }}" 
                        target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-link me-1"></i>Google Maps
                        </a> --}}
                    </div>
                </div>
                
                <script>
                    (function() {
                        function initMap{{ $mapId }}() {
                            // Wait for container to be visible
                            const container = document.getElementById('{{ $mapId }}');
                            if (!container || container.offsetWidth === 0) {
                                setTimeout(initMap{{ $mapId }}, 100);
                                return;
                            }
                            
                            // Initialize map
                            var map{{ $mapId }} = L.map('{{ $mapId }}', {
                                zoomControl: true,
                                attributionControl: true
                            }).setView([{{ $coordinates['lat'] }}, {{ $coordinates['lng'] }}], 15);
                            
                            // Add tile layer with error handling
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '© OpenStreetMap contributors',
                                maxZoom: 19,
                                subdomains: ['a', 'b', 'c']
                            }).addTo(map{{ $mapId }});
                            
                            // Add marker
                            L.marker([{{ $coordinates['lat'] }}, {{ $coordinates['lng'] }}])
                                .addTo(map{{ $mapId }})
                                .bindPopup('Lokasi: {{ number_format($coordinates['lat'], 6) }}, {{ number_format($coordinates['lng'], 6) }}');
                            
                            // Force map to resize and invalidate after a short delay
                            setTimeout(function() {
                                map{{ $mapId }}.invalidateSize();
                            }, 100);
                            
                            // Disable interaction for display mode
                            map{{ $mapId }}.dragging.disable();
                            map{{ $mapId }}.touchZoom.disable();
                            map{{ $mapId }}.doubleClickZoom.disable();
                            map{{ $mapId }}.scrollWheelZoom.disable();
                            map{{ $mapId }}.boxZoom.disable();
                            map{{ $mapId }}.keyboard.disable();
                            if (map{{ $mapId }}.tap) map{{ $mapId }}.tap.disable();
                            
                            // Add click to enable interaction
                            container.addEventListener('click', function() {
                                map{{ $mapId }}.dragging.enable();
                                map{{ $mapId }}.touchZoom.enable();
                                map{{ $mapId }}.doubleClickZoom.enable();
                                map{{ $mapId }}.scrollWheelZoom.enable();
                                map{{ $mapId }}.boxZoom.enable();
                                map{{ $mapId }}.keyboard.enable();
                                if (map{{ $mapId }}.tap) map{{ $mapId }}.tap.enable();
                                
                                // Invalidate size again when enabling interaction
                                setTimeout(function() {
                                    map{{ $mapId }}.invalidateSize();
                                }, 50);
                            });
                        }
                        
                        // Initialize when DOM is ready
                        if (document.readyState === 'loading') {
                            document.addEventListener('DOMContentLoaded', initMap{{ $mapId }});
                        } else {
                            initMap{{ $mapId }}();
                        }
                    })();
                </script>
            @else
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    Format koordinat tidak valid
                </div>
            @endif
        @else
            <div class="text-center p-3 border rounded bg-light">
                <i class="bi bi-map fa-2x text-muted mb-2"></i>
                <br>
                <span class="text-muted">Lokasi tidak diset</span>
            </div>
        @endif
        @break
        
        
    @case('file')
        @if($value)
            <a href="{{ asset($value) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-download"></i> Download File
            </a>
        @else
            <span class="text-muted">Tidak ada file</span>
        @endif
        @break
        
    @case('image')
        @if($value)
            <div>
                <img src="{{ asset($value) }}" alt="Image" class="img-thumbnail" 
                     style="max-width: {{ isset($compact) ? '100px' : '200px' }}; max-height: {{ isset($compact) ? '100px' : '200px' }};">
                <br>
                <a href="{{ asset($value) }}" target="_blank" class="btn btn-outline-primary btn-sm mt-2">
                    <i class="bi bi-link"></i> Lihat Penuh
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
                
    @case('signing')
        @if($value)
                <img src="{{ $value }}" alt="Signature" style="max-width: 300px; border:1px solid #ccc;">
        @endif
        @break

    @default
        <span>{{ $value }}</span>
@endswitch