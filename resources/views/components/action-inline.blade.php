<div class="d-flex gap-2 mb-0">
    @foreach($show as $action)
        @if($canShow[$action] ?? false) 
            @if($action === 'delete') 
                 <form id="delete-form-{{ $model->id }}" 
                        action="{{ $getRoute($action) }}" 
                        method="POST" 
                        style="display:none;">
                    @csrf
                    @method('DELETE')
                </form>

                <a href="javascript:void(0)" 
                    class="btn text-danger fw-normal text-capitalize" data-bs-toggle="tooltip" data-bs-original-title="{{ $getLabel($action) }}"
                    onclick="confirmDelete({{ $model->id }})">
                    <i class="{{ $getIcon($action) }}"></i> 
                </a>
            @else
                <a href="{{ $getRoute($action) }}" class="btn fw-normal text-capitalize" data-bs-toggle="tooltip" data-bs-original-title="{{ $getLabel($action) }}">
                    <i class="{{ $getIcon($action) }}"></i> 
                </a>
            @endif 
        @endif
    @endforeach
</div> 
