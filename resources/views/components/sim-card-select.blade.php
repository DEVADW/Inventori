@props(['options', 'selected' => []])

<div>
    <label for="sim_card_ids" class="form-label">No SIM Card <span class="text-muted small">(bisa pilih lebih dari satu)</span></label>
    <select id="sim_card_ids" name="sim_card_ids[]" class="form-select @error('sim_card_ids.*') is-invalid @enderror" multiple>
        @foreach ($options as $simCard)
            <option value="{{ $simCard->id }}" {{ in_array($simCard->id, $selected) ? 'selected' : '' }}>
                {{ $simCard->nomor_msisdn }}
            </option>
        @endforeach
    </select>
    @error('sim_card_ids.*')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

@once
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/styles/choices.min.css">
    @endpush
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/scripts/choices.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const el = document.getElementById('sim_card_ids');
                if (el) {
                    new Choices(el, {
                        searchEnabled: true,
                        searchPlaceholderValue: 'Cari nomor SIM Card...',
                        noResultsText: 'Tidak ada nomor SIM Card yang cocok',
                        itemSelectText: '',
                        removeItemButton: true,
                        shouldSort: false,
                    });
                }
            });
        </script>
    @endpush
@endonce
