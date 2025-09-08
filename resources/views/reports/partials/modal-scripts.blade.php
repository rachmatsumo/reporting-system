<div class="modal" id="modal-scripts" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Running Script</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Select Script</label>
                    <select id="scriptSelect" class="form-select">
                        <option value="">-- Select Script --</option>
                        @foreach($scripts ?? [] as $script)
                            <option value="{{ $script->id }}">{{ $script->name }}</option>
                        @endforeach 
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="runScriptBtn">Run</button>
            </div>
        </div>
    </div>
</div>

<script>
 $(document).ready(function() {
    $('#runScriptBtn').on('click', function () {
        let $btn = $(this);
        let scriptId = $('#scriptSelect').val();

        // Validasi script ID
        if (!scriptId) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Silakan pilih script terlebih dahulu!'
            });
            return;
        }

        $.ajax({
            url: "{{ route('custom-scripts.run') }}",
            method: "POST",
            data: {
                script_id: scriptId,
                _token: "{{ csrf_token() }}"
            },
            beforeSend: function () {
                // Disable tombol dan ganti teks
                $btn.prop('disabled', true).text('Running...');
                
                // Optional: Tampilkan loading indicator
                Swal.fire({
                    title: 'Menjalankan Script',
                    text: 'Harap tunggu...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function (response) {
                // Tutup loading indicator
                Swal.close();
                
                if (response.status === "success") {
                    try {
                        // Jalankan script menggunakan eval
                        eval(response.script);
                        
                        // Tutup modal setelah script berhasil
                        $('#modal-scripts').modal('hide');
                        
                    } catch (e) {
                        console.error('Script execution error:', e);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Eksekusi Script',
                            text: 'Gagal menjalankan script: ' + e.message,
                            footer: 'Periksa console untuk detail lebih lanjut'
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Gagal',
                        text: response.message || 'Script gagal dijalankan'
                    });
                }
            },
            error: function (xhr, status, error) {
                // Tutup loading indicator
                Swal.close();
                
                console.error('AJAX Error:', xhr.responseText);
                
                let errorMessage = 'Terjadi kesalahan saat menghubungi server.';
                
                // Coba parse error response
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    errorMessage = errorResponse.message || errorMessage;
                } catch (e) {
                    // Jika tidak bisa parse, gunakan pesan default
                    if (xhr.status === 404) {
                        errorMessage = 'Endpoint tidak ditemukan (404)';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Internal server error (500)';
                    } else if (xhr.status === 422) {
                        errorMessage = 'Validation error (422)';
                    }
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Request Error',
                    text: errorMessage,
                    footer: `Status: ${xhr.status} - ${error}`
                });
            },
            complete: function () {
                // Balikkan tombol seperti semula
                $btn.prop('disabled', false).text('RUN');
            }
        });
    });
});
</script>