<div class="mb-2">
    <label class="block font-semibold">Icon</label>

    <select id="icon-select" name="icon" class="form-control w-full">
        @foreach (bootstrap_icons() as $icon)
            <option value="{{ $icon }}" 
                {{ $selectedIcon === "$icon" ? 'selected' : '' }}>
                {{ $icon }}
            </option>
        @endforeach
    </select>

    @error('icon')
        <p class="text-red-600 text-sm">{{ $message }}</p>
    @enderror
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
<style>
    .ts-control{
        border :none;
        padding :2px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    let el = document.getElementById("icon-select");
    if (el && !el.tomselect) {   // <-- cek apakah sudah di-init
        new TomSelect(el, {
            render: {
                option: function(data, escape) {
                    return `<div>
                                <i class="bi bi-${escape(data.value)}"></i>
                                <span class="ms-2">${escape(data.text)}</span>
                            </div>`;
                },
                item: function(data, escape) {
                    return `<div>
                                <i class="bi bi-${escape(data.value)}"></i>
                                <span class="ms-2">${escape(data.text)}</span>
                            </div>`;
                }
            }
        });
    }
});
</script>

@endpush
