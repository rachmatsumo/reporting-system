<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="{{ route($routePrefix.'.index') }}" class="btn btn-secondary">
        <i class="bi bi-x-lg"></i> Batal
    </a>

    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg"></i>
        {{ $mode === 'edit' ? 'Update' : 'Simpan' }}
    </button>
</div>
