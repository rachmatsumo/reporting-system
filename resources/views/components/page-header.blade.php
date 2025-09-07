 @php
    $segments = Request::segments();
    $url = ''; 
@endphp
<div class="card-header d-flex justify-content-between align-items-center border-bottom mb-3 py-3 px-1">
    <h5 class="mb-0">{{ $title }}</h5>

    @if($mode === 'index')
        <a href="{{ route($routePrefix.'.create') }}" class="btn btn-sm btn-primary mb-0">
            <i class="bi bi-plus-lg"></i> Tambah {{ Str::singular(ucfirst($routePrefix)) }}
        </a>
    @elseif($mode === 'show')
        <div class="d-flex gap-2 align-items-center">
            <a href="{{ route($routePrefix.'.index') }}" class="btn btn-sm btn-secondary mb-0">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <a href="{{ route($routePrefix.'.edit', $segments[1]) }}" class="btn btn-sm btn-success mb-0">
                <i class="bi bi-pencil"></i> Edit
            </a>
        </div> 
    @elseif($mode === 'onlyShow') 
        <a href="{{ route($routePrefix.'.edit', $segments[1]) }}" class="btn btn-sm btn-success mb-0">
            <i class="bi bi-pencil"></i> Edit
        </a>
    @elseif($mode === 'editNoIndex')
        <a href="{{ route($routePrefix.'.show', $segments[1]) }}" class="btn btn-sm btn-secondary mb-0">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    @else
        <a href="{{ route($routePrefix.'.index') }}" class="btn btn-sm btn-secondary mb-0">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    @endif
</div>
