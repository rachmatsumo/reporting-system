@extends('layouts.user_type.auth')
@section('title', 'Report')
@section('content')

<div class="container">
    <x-page-header route-prefix="reports" mode="index" />
                        
    <!-- Filters -->
    <div class="row mb-2">
        <div class="col-md-3 mb-2">
            <select class="form-select form-select-sm" id="filterDesign" name="report_design_id">
                <option value="">All Report</option>
                @foreach($reportDesigns ?? [] as $design)
                    <option value="{{ $design->id }}" 
                        {{ request('report_design_id') == $design->id ? 'selected' : '' }}>
                        {{ $design->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2 mb-2">
            <select class="form-select form-select-sm" id="filterStatus">
                <option value="">Semua Status</option>
                <option value="draft"     {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                <option value="approved"  {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected"  {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>

        <div class="col-md-2 mb-2">
            <input type="date" class="form-control form-control-sm" id="filterDateFrom" 
                value="{{ request('date_from') }}" placeholder="Dari Tanggal">
        </div>

        <div class="col-md-2 mb-2">
            <input type="date" class="form-control form-control-sm" id="filterDateTo" 
                value="{{ request('date_to') }}" placeholder="Sampai Tanggal">
        </div>

        <div class="col-md-3">
            <button type="button" class="btn btn-sm btn-outline-primary mb-2" onclick="applyFilters()">
                <i class="bi bi-search"></i> Filter
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary mb-2" onclick="resetFilters()">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </button> 
            <button type="button" class="btn btn-sm btn-success mb-2"
                onclick="window.location.href='{{ route('reports.export.list', request()->query()) }}'">
                <i class="bi bi-file-earmark-excel"></i> Excel
            </button>

        </div>
    </div>
    <div class="row mb-4">

        {{-- Search --}}
        <div class="col-md-3 mb-2">
            <input type="text" class="form-control form-control-sm" 
                id="filterSearch"
                placeholder="Cari report..."
                value="{{ request('search') }}">
        </div>

        {{-- Per Page --}}
        <div class="col-md-2 mb-2">
            <select class="form-select form-select-sm" id="filterPerPage">
                @foreach([10, 20, 50, 100, 500, 1000, 'Tampilkan semua'] as $size)
                    <option value="{{ $size }}" 
                        {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                        {{ $size }} {{ $size!=='Tampilkan semua' ? 'Per halaman' : '' }}
                    </option>
                @endforeach
            </select>
        </div>

    </div>

    @if($reports->count() > 0)

        <!-- Reports Table -->
        <div class="table-responsive" style="height: 300px; overflow-y: auto;">
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
                            @if($report->status === 'approved' || $report->status === 'submitted')
                                <x-action-dropdown :model="$report" :show="['view']"/>
                            @else
                                <x-action-dropdown :model="$report" :show="['view', 'edit', 'delete']"/>
                            @endif
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
                    <i class="bi bi-trash"></i> Hapus Terpilih
                </button>
                <button type="button" class="btn btn-outline-success btn-sm" 
                        onclick="bulkAction('approve')" id="bulkApproveBtn" style="display: none;">
                    <i class="bi bi-check"></i> Approve Terpilih
                </button>
                <button type="button" class="btn btn-outline-warning btn-sm" 
                        onclick="bulkAction('reject')" id="bulkRejectBtn" style="display: none;">
                    <i class="bi bi-x"></i> Reject Terpilih
                </button>
            </div>
            
            <!-- Pagination -->
            @if($reports instanceof \Illuminate\Pagination\LengthAwarePaginator)
                {{ $reports->links() }}
            @endif
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
            <p class="text-muted">No report available</p>
            <a href="{{ route('reports.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Create a first report
            </a>
        </div>
    @endif
</div>
             

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
        const search = document.getElementById('filterSearch').value;
        const perPage = document.getElementById('filterPerPage').value;
        
        const params = new URLSearchParams();
        if (design) params.append('report_design_id', design);
        if (status) params.append('status', status);
        if (dateFrom) params.append('date_from', dateFrom);
        if (dateTo) params.append('date_to', dateTo);
        if (search) params.append('search', search);
        if (perPage) params.append('per_page', perPage);
        
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
        font-size : 0.850rem;
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