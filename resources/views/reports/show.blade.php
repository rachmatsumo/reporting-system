@extends('layouts.user_type.auth')
@section('title', 'Lihat Report')

@section('content') 
<div class="container-fluid">
    <div class="row">
         
        <div class="col-12 mb-4 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">{{ $report->reportDesign->name }}</h5>
                <p class="text-muted mb-0 fs-7"> 
                    <i class="fa fa-calendar"></i> {{ $report->created_at->format('d/m/Y H:i') }} <br>
                    <i class="fa fa-user"></i> {{ $report->creator->name ?? 'Unknown' }}
                </p>
            </div>
            <div>
                @switch($report->status)
                    @case('draft')
                        <span class="badge bg-secondary fs-8">Draft</span>
                        @break
                    @case('submitted')
                        <span class="badge bg-warning fs-8">Submitted</span>
                        @break
                    @case('approved')
                        <span class="badge bg-success fs-8">Approved</span>
                        @break
                    @case('rejected')
                        <span class="badge bg-danger fs-8">Rejected</span>
                        @break
                @endswitch
            </div>
        </div>
        <div class="col-12 mb-4 ">
            
            <!-- Action Buttons -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    @if($report->status === 'draft' || $report->status ==='rejected' || Auth::user()->can('edit-reports'))
                        <a href="{{ route('reports.edit', $report) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                    @endif
                    <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                        <i class="bi bi-printer"></i> Print
                    </button>
                </div>
                <div> 
                    <a href="{{ route('reports.export.pdf', $report->id) }}" 
                        class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-file-earmark-pdf"></i> PDF
                    </a>
                    {{-- <a href="{{ route('reports.export', [$report, 'excel']) }}" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-file-earmark-excel"></i> Excel
                    </a> --}}
                    {{-- <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a> --}}
                </div>
            </div>

            <!-- Tab Navigation -->
            <ul class="nav nav-tabs" id="reportTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="main-data-tab" data-bs-toggle="tab" 
                            data-bs-target="#main-data" type="button" role="tab">
                        <i class="bi bi-file-post me-2"></i>General Information
                    </button>
                </li>
                @foreach($report->reportDesign->subDesigns as $subDesign)
                    @php
                        $subDataCount = $report->subData->where('report_sub_design_id', $subDesign->id)->count();
                    @endphp
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sub-{{ $subDesign->id }}-tab" data-bs-toggle="tab" 
                                data-bs-target="#sub-{{ $subDesign->id }}" type="button" role="tab">
                            <i class="bi bi-file-earmark me-2"></i>{{ $subDesign->name }}
                            <span class="badge bg-secondary ms-1">{{ $subDataCount }}</span>
                        </button>
                    </li>
                @endforeach
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="reportTabsContent">
                <!-- Main Data Tab -->
                <div class="tab-pane fade show active" id="main-data" role="tabpanel">
                    <div class="mt-4">
                        @if($report->reportDesign->fields->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tbody>
                                        @foreach($report->reportDesign->fields as $field)
                                            @php
                                                $fieldValue = $report->data[$field->name] ?? null;
                                            @endphp
                                            <tr>
                                                <td width="30%" class="fw-bold">{{ $field->label }}</td>
                                                <td>
                                                    @if($fieldValue !== null && $fieldValue !== '')
                                                        @include('reports.partials.field-display', [
                                                            'field' => $field, 
                                                            'value' => $fieldValue
                                                        ])
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                                <p class="text-muted">Tidak ada main fields dalam template ini</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sub Reports Tabs -->
                @foreach($report->reportDesign->subDesigns as $subDesign)
                    @php
                        $subDataItems = $report->subData->where('report_sub_design_id', $subDesign->id);
                    @endphp
                    <div class="tab-pane fade" id="sub-{{ $subDesign->id }}" role="tabpanel">
                        <div class="mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="mb-0">{{ $subDesign->name }}</h6>
                                    @if($subDesign->description)
                                        <small class="text-muted">{{ $subDesign->description }}</small>
                                    @endif
                                </div>
                                <span class="badge bg-info">{{ $subDataItems->count() }} entries</span>
                            </div>

                            @if($subDataItems->count() > 0)
                                @if($subDesign->type === 'table')
                                    <!-- Table Display -->
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="5%">#</th>
                                                    @foreach($subDesign->fields as $subField)
                                                        <th>{{ $subField->label }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($subDataItems->sortBy('row_index') as $subData)
                                                <tr>
                                                    <td>{{ $subData->row_index + 1 }}</td>
                                                    @foreach($subDesign->fields as $subField)
                                                        @php
                                                            $subFieldValue = $subData->data[$subField->name] ?? null;
                                                        @endphp
                                                        <td>
                                                            @if($subFieldValue !== null && $subFieldValue !== '')
                                                                @include('reports.partials.field-display', [
                                                                    'field' => $subField, 
                                                                    'value' => $subFieldValue,
                                                                    'compact' => true
                                                                ])
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <!-- Card Display -->
                                    <div class="row">
                                        @foreach($subDataItems->sortBy('row_index') as $subData)
                                        <div class="col-md-12 mb-3">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h6 class="mb-0">{{ $subDesign->name }} #{{ $subData->row_index + 1 }}</h6>
                                                </div>
                                                <div class="card-body">
                                                    <table class="table table-sm">
                                                        <tbody>
                                                            @foreach($subDesign->fields as $subField)
                                                                @php
                                                                    $subFieldValue = $subData->data[$subField->name] ?? null;
                                                                @endphp
                                                                <tr>
                                                                    <td width="40%" class="fw-bold">{{ $subField->label }}</td>
                                                                    <td>
                                                                        @if($subFieldValue !== null && $subFieldValue !== '')
                                                                            @include('reports.partials.field-display', [
                                                                                'field' => $subField, 
                                                                                'value' => $subFieldValue
                                                                            ])
                                                                        @else
                                                                            <span class="text-muted">-</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-4">
                                    <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                                    <p class="text-muted">Tidak ada data untuk sub report ini</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
            
    </div>
</div>
@endsection
 
@push('styles')
<style>
@media print {
    .btn, .nav-tabs, .card-header .btn, .text-end, .navbar {
        display: none !important;
    }
    
    .main-content {
        margin: 0 !important;
        padding: 0 !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .tab-content > .tab-pane {
        display: block !important;
        opacity: 1 !important;
    }
    
    .tab-pane:not(.active) {
        page-break-before: always;
    }
}

.table td {
    vertical-align: middle;
}

.fw-bold {
    font-weight: 600 !important;
}

.fs-6 {
    font-size: 1rem !important;
}

.badge.fs-6 {
    padding: 0.5rem 0.75rem;
}

.table-striped > tbody > tr:nth-of-type(odd) > td {
    background-color: rgba(0,0,0,0.025);
}
</style>

@endpush