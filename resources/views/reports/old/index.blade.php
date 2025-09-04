@extends('layouts.user_type.auth')
 
@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <div class="d-flex flex-row justify-content-between">
                            <h5 class="mb-0">Reports</h5>
                            <div>
                                <a href="{{ route('reports.create') }}" class="btn btn-primary mb-0">
                                    <i class="fas fa-plus"></i> Create New Report
                                </a>
                                <a href="{{ route('report-design.index') }}" class="btn btn-outline-secondary mb-0">
                                    <i class="fas fa-cog"></i> Manage Report Designs
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        
                        <!-- Filters -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <select class="form-select" id="filterDesign">
                                    <option value="">All Report</option>
                                    @foreach($reportDesigns ?? [] as $design)
                                        <option value="{{ $design->id }}">{{ $design->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" id="filterStatus">
                                    <option value="">Semua Status</option>
                                    <option value="draft">Draft</option>
                                    <option value="submitted">Submitted</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" class="form-control" id="filterDateFrom" placeholder="Dari Tanggal">
                            </div>
                            <div class="col-md-2">
                                <input type="date" class="form-control" id="filterDateTo" placeholder="Sampai Tanggal">
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-outline-primary" onclick="applyFilters()">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="resetFilters()">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                            </div>
                        </div>

                        @if($reports->count() > 0)
                            <!-- Reports Table -->
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">
                                                <input type="checkbox" class="form-check-input" id="selectAll">
                                            </th>
                                            {{-- <th width="25%">Judul Report</th> --}}
                                            <th>Report</th>
                                            <th>Status</th>
                                            <th>Dibuat Oleh</th>
                                            <th>Tanggal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reports as $report)
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" class="form-check-input report-checkbox" 
                                                       value="{{ $report->id }}">
                                            </td>
                                            {{-- <td>
                                                <strong>{{ $report->title }}</strong>
                                                @if($report->reportDesign->subDesigns->count() > 0)
                                                    <br>
                                                    <small class="text-muted">
                                                        @php
                                                            $subDataCount = $report->subData()->count();
                                                        @endphp
                                                        {{ $subDataCount }} sub-report entries
                                                    </small>
                                                @endif
                                            </td> --}}
                                            <td>
                                                {{ $report->reportDesign->name }}
                                                {{-- <span class="badge bg-info">{{ $report->reportDesign->name }}</span> --}}
                                                {{-- <br>
                                                <small class="text-muted">
                                                    {{ $report->reportDesign->fields->count() }} main fields
                                                    @if($report->reportDesign->subDesigns->count() > 0)
                                                        <br>{{ $report->reportDesign->subDesigns->count() }} sub-reports
                                                    @endif
                                                </small> --}}
                                            </td>
                                            <td>
                                                @switch($report->status)
                                                    @case('draft')
                                                        <span class="badge bg-secondary">Draft</span>
                                                        @break
                                                    @case('submitted')
                                                        <span class="badge bg-warning">Submitted</span>
                                                        @break
                                                    @case('approved')
                                                        <span class="badge bg-success">Approved</span>
                                                        @break
                                                    @case('rejected')
                                                        <span class="badge bg-danger">Rejected</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ ucfirst($report->status) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                {{ $report->creator->name ?? 'Unknown' }} 
                                            </td>
                                            <td>
                                                {{ $report->created_at->format('d/m/Y') }}
                                                <br>
                                                <small class="text-muted">{{ $report->created_at->format('H:i') }}</small>
                                            </td>
                                            <td>
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
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Bulk Actions -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <button type="button" class="btn btn-outline-danger btn-sm" 
                                            onclick="bulkAction('delete')" id="bulkDeleteBtn" style="display: none;">
                                        <i class="fas fa-trash"></i> Hapus Terpilih
                                    </button>
                                    <button type="button" class="btn btn-outline-success btn-sm" 
                                            onclick="bulkAction('approve')" id="bulkApproveBtn" style="display: none;">
                                        <i class="fas fa-check"></i> Approve Terpilih
                                    </button>
                                    <button type="button" class="btn btn-outline-warning btn-sm" 
                                            onclick="bulkAction('reject')" id="bulkRejectBtn" style="display: none;">
                                        <i class="fas fa-times"></i> Reject Terpilih
                                    </button>
                                </div>
                                
                                <!-- Pagination -->
                                <div>
                                    {{ $reports->links() }}
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No report available</p>
                                <a href="{{ route('reports.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create a first report
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus report ini?</p>
                <p class="text-danger"><strong>Tindakan ini tidak dapat dibatalkan!</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Action Modal -->
<div class="modal fade" id="bulkActionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkActionTitle">Konfirmasi Bulk Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="bulkActionMessage"></p>
                <p class="text-warning"><strong>Tindakan ini akan mempengaruhi beberapa report sekaligus!</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="bulkActionForm" method="POST" action="{{ route('reports.bulk-action') }}" style="display: inline;">
                    @csrf
                    <input type="hidden" name="action" id="bulkActionType">
                    <input type="hidden" name="report_ids" id="bulkActionIds">
                    <button type="submit" class="btn btn-primary" id="bulkActionConfirm">Konfirmasi</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(reportId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/reports/${reportId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function bulkAction(action) {
    const selectedReports = Array.from(document.querySelectorAll('.report-checkbox:checked'))
                                .map(cb => cb.value);
    
    if (selectedReports.length === 0) {
        alert('Pilih minimal satu report untuk melakukan bulk action');
        return;
    }

    let title, message, buttonClass;
    
    switch(action) {
        case 'delete':
            title = 'Konfirmasi Hapus Bulk';
            message = `Apakah Anda yakin ingin menghapus ${selectedReports.length} report(s)?`;
            buttonClass = 'btn-danger';
            break;
        case 'approve':
            title = 'Konfirmasi Approve Bulk';
            message = `Apakah Anda yakin ingin approve ${selectedReports.length} report(s)?`;
            buttonClass = 'btn-success';
            break;
        case 'reject':
            title = 'Konfirmasi Reject Bulk';
            message = `Apakah Anda yakin ingin reject ${selectedReports.length} report(s)?`;
            buttonClass = 'btn-warning';
            break;
    }

    document.getElementById('bulkActionTitle').textContent = title;
    document.getElementById('bulkActionMessage').textContent = message;
    document.getElementById('bulkActionType').value = action;
    document.getElementById('bulkActionIds').value = JSON.stringify(selectedReports);
    
    const confirmBtn = document.getElementById('bulkActionConfirm');
    confirmBtn.className = `btn ${buttonClass}`;
    confirmBtn.textContent = 'Ya, ' + action.charAt(0).toUpperCase() + action.slice(1);

    const modal = new bootstrap.Modal(document.getElementById('bulkActionModal'));
    modal.show();
}

function applyFilters() {
    const design = document.getElementById('filterDesign').value;
    const status = document.getElementById('filterStatus').value;
    const dateFrom = document.getElementById('filterDateFrom').value;
    const dateTo = document.getElementById('filterDateTo').value;
    
    const params = new URLSearchParams();
    if (design) params.append('design', design);
    if (status) params.append('status', status);
    if (dateFrom) params.append('date_from', dateFrom);
    if (dateTo) params.append('date_to', dateTo);
    
    window.location.href = '{{ route("reports.index") }}?' + params.toString();
}

function resetFilters() {
    window.location.href = '{{ route("reports.index") }}';
}

// Checkbox management
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const reportCheckboxes = document.querySelectorAll('.report-checkbox');
    const bulkButtons = ['bulkDeleteBtn', 'bulkApproveBtn', 'bulkRejectBtn'];

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        reportCheckboxes.forEach(cb => {
            cb.checked = this.checked;
        });
        toggleBulkButtons();
    });

    // Individual checkbox change
    reportCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.report-checkbox:checked').length;
            selectAllCheckbox.checked = checkedCount === reportCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < reportCheckboxes.length;
            toggleBulkButtons();
        });
    });

    function toggleBulkButtons() {
        const checkedCount = document.querySelectorAll('.report-checkbox:checked').length;
        bulkButtons.forEach(btnId => {
            const btn = document.getElementById(btnId);
            if (btn) {
                btn.style.display = checkedCount > 0 ? 'inline-block' : 'none';
            }
        });
    }
});
</script>

<style>
.table th {
    font-weight: 600;
    font-size: 0.875rem;
    border-bottom: 2px solid #dee2e6;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75rem;
}

.dropdown-menu {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.btn-group .btn {
    border-radius: 0;
}

.btn-group .btn:first-child {
    border-top-left-radius: 0.375rem;
    border-bottom-left-radius: 0.375rem;
}

.btn-group .btn:last-child {
    border-top-right-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
}
</style>

@endsection