<div class="dropdown">
    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" 
            type="button" data-bs-toggle="dropdown">
        <i class="fas fa-ellipsis-v"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        <li>
            <a class="dropdown-item" href="{{ route('reports.show', $report) }}">
                <i class="fas fa-eye me-2"></i>Lihat
            </a>
        </li>
        @if($report->status === 'draft' || Auth::user()->can('edit-reports'))
            <li>
                <a class="dropdown-item" href="{{ route('reports.edit', $report) }}">
                    <i class="fas fa-edit me-2"></i>Edit
                </a>
            </li>
        @endif
        <li>
            <a class="dropdown-item" href="{{ route('reports.export', [$report, 'pdf']) }}">
                <i class="fas fa-file-pdf me-2"></i>Export PDF
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('reports.export', [$report, 'excel']) }}">
                <i class="fas fa-file-excel me-2"></i>Export Excel
            </a>
        </li>
        @if(Auth::user()->can('delete-reports'))
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item text-danger" href="#" 
                    onclick="confirmDelete({{ $report->id }})">
                    <i class="fas fa-trash me-2"></i>Hapus
                </a>
            </li>
        @endif
    </ul>
</div>