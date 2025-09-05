<!-- Map Modal - Add this before closing </main> -->
<div class="modal fade" id="mapModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-map-marked-alt me-2"></i>
                    Pilih Lokasi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-8">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="getCurrentLocation()" id="getCurrentLocation">
                            <i class="fas fa-crosshairs me-2"></i>Gunakan Lokasi Saat Ini
                        </button>
                    </div>
                </div>
                
                <div id="mapContainer" style="height: 400px; width: 100%;"></div>
                
                <div class="mt-3 p-3 bg-light rounded">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div id="selectedCoordinates">
                                <i class="fas fa-crosshairs me-2"></i>
                                <span class="text-muted">Klik pada peta untuk memilih lokasi</span>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <button type="button" class="btn btn-success" onclick="confirmMapSelection()" id="confirmLocation" disabled>
                                <i class="fas fa-check me-2"></i>Konfirmasi Lokasi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>