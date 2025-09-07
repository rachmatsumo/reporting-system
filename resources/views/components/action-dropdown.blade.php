<button class="btn btn-outline-secondary btn-sm dropdown-toggle mb-0" 
        type="button" data-bs-toggle="dropdown">
    <i class="bi bi-three-dots-vertical"></i>
</button>

<ul class="dropdown-menu dropdown-menu-end border">
    @foreach($show as $action)
        @if($canShow[$action] ?? false)
            <li>
                @if($action === 'delete')
                    <form id="delete-form-{{ $model->id }}" 
                          action="{{ $getRoute($action) }}" 
                          method="POST" 
                          style="display:none;">
                        @csrf
                        @method('DELETE')
                    </form>

                    <a href="javascript:void(0)" 
                       class="dropdown-item text-danger" 
                       onclick="confirmDelete({{ $model->id }})">
                        <i class="{{ $getIcon($action) }} me-2"></i>
                        {{ $getLabel($action) }}
                    </a>
                @else
                    <a href="{{ $getRoute($action) }}" class="dropdown-item">
                        <i class="{{ $getIcon($action) }} me-2"></i>
                        {{ $getLabel($action) }}
                    </a>
                @endif
            </li>
        @endif
    @endforeach
</ul>

{{-- <ul class="dropdown-menu dropdown-menu-end border">
    @foreach($show as $action)
        @if($canShow[$action] ?? false)
            <li>
                @if($action === 'delete')
                    <form action="{{ $getRoute($action) }}" method="POST" onsubmit="return confirm('Yakin ingin hapus data ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="{{ $getIcon($action) }} me-2"></i>
                            {{ $getLabel($action) }}
                        </button>
                    </form>
                @else
                    <a href="{{ $getRoute($action) }}" class="dropdown-item">
                        <i class="{{ $getIcon($action) }} me-2"></i>
                        {{ $getLabel($action) }}
                    </a>
                @endif
            </li>
        @endif
    @endforeach
</ul> --}}
