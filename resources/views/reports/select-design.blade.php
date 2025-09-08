@extends('layouts.user_type.auth')
@section('title', 'Pilih Report Design')
@section('content')
 
    <div class="container">
        <div class="row"> 
            <div class="col-12 border-bottom mb-4">
                <h6 class="mb-0">Pilih Template Report</h6>
                <p class="text-muted mb-0">Pilih template report yang ingin Anda gunakan</p>
            </div>
            <div class="col-12 mb-4">
                
                @if($reportDesigns->count() > 0)
                    <div class="row">
                        @foreach($reportDesigns as $design)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 report-design-card" onclick="selectDesign({{ $design->id }})">
                                <div class="card-body d-flex flex-column">
                                    <div class="mb-3">
                                        <h6 class="card-title">{{ $design->name }}</h6>
                                        @if($design->description)
                                            <p class="card-text text-muted small">{{ Str::limit($design->description, 100) }}</p>
                                        @endif
                                    </div>
                                    
                                    <div class="mt-auto">
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <div class="border-end">
                                                    <div class="h5 mb-0 text-primary">{{ $design->fields->count() }}</div>
                                                    <small class="text-muted">Main Fields</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="h5 mb-0 text-info">{{ $design->subDesigns->count() }}</div>
                                                <small class="text-muted">Sub Reports</small>
                                            </div>
                                        </div>
                                        
                                        @if($design->subDesigns->count() > 0)
                                            <hr class="my-2">
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($design->subDesigns as $subDesign)
                                                    <span class="badge bg-light text-dark border">{{ $subDesign->name }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                        
                                        <div class="mt-3">
                                            <button class="btn btn-primary btn-sm w-100">
                                                <i class="bi bi-plus-lg me-2"></i>Gunakan Template
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card-footer text-center">
                                    <small class="text-muted">
                                        <i class="bi bi-clock-history me-1"></i>
                                        Dibuat {{ $design->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Quick Stats -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-3">
                                            <div class="h4 mb-0 text-primary">{{ $reportDesigns->count() }}</div>
                                            <small class="text-muted">Total Templates</small>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="h4 mb-0 text-success">{{ $reportDesigns->sum(fn($d) => $d->fields->count()) }}</div>
                                            <small class="text-muted">Total Main Fields</small>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="h4 mb-0 text-info">{{ $reportDesigns->sum(fn($d) => $d->subDesigns->count()) }}</div>
                                            <small class="text-muted">Total Sub Reports</small>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="h4 mb-0 text-warning">
                                                {{ $reportDesigns->sum(fn($d) => $d->subDesigns->sum(fn($s) => $s->fields->count())) }}
                                            </div>
                                            <small class="text-muted">Total Sub Fields</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-palette fs-3 text-muted mb-3"></i>
                        <h6 class="text-muted">Belum ada template report tersedia</h6>
                        <p class="text-muted">Anda perlu membuat template report terlebih dahulu sebelum dapat membuat report</p>
                        <a href="{{ route('report-design.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg"></i> Buat Template Report
                        </a>
                    </div>
                @endif
                
                <div class="text-end mt-4">
                    <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

            </div> 
        </div>
    </div>
 

<script>
function selectDesign(designId) {
    window.location.href = `/reports/create/${designId}`;
}

// Add hover effects
document.addEventListener('DOMContentLoaded', function() {
    const designCards = document.querySelectorAll('.report-design-card');
    
    designCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.1)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
        });
    });
});
</script>

<style>
.report-design-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid #e9ecef;
}

.report-design-card:hover {
    border-color: #007bff;
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.report-design-card .card-body {
    min-height: 200px;
}

.border-end {
    border-right: 1px solid #dee2e6 !important;
}

.badge.bg-light {
    color: #495057 !important;
    border: 1px solid #dee2e6;
}

.card-footer {
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
}

.h4, .h5, .h6 {
    font-weight: 600;
}

.bg-light.card {
    background-color: #f8f9fa !important;
}
</style>

@endsection