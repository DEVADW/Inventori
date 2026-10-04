@php
    $storedItems = old('item_pemeriksaan', $icc->item_pemeriksaan ?? []);
    $itemsByName = collect($storedItems)->keyBy('nama');
@endphp
@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Data belum lengkap!</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="row g-3">
    <div class="col-12">
        <h6 class="text-primary fw-bold border-bottom pb-2">Informasi Unit</h6>
    </div>
    <div class="col-md-4"><label for="no_lambung_unit" class="form-label">No. Lambung Unit <span
                class="text-danger">*</span></label><input id="no_lambung_unit" name="no_lambung_unit"
            class="form-control @error('no_lambung_unit') is-invalid @enderror"
            value="{{ old('no_lambung_unit', $icc->no_lambung_unit ?? '') }}" required autocomplete="off"
            data-unit-select2>
        <div id="unitSearchDropdown" class="dropdown-menu w-100 shadow-sm border mt-1"
            style="display:none; max-height:260px; overflow:auto; z-index:1060;">
            <div class="list-group list-group-flush" id="unitSearchResults"></div>
        </div>
        @error('no_lambung_unit')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4"><label for="tanggal_inspeksi" class="form-label">Tanggal Inspeksi <span
                class="text-danger">*</span></label><input id="tanggal_inspeksi" type="date" name="tanggal_inspeksi"
            class="form-control @error('tanggal_inspeksi') is-invalid @enderror"
            value="{{ old('tanggal_inspeksi', isset($icc) ? $icc->tanggal_inspeksi?->format('Y-m-d') : now()->format('Y-m-d')) }}"
            required>
        @error('tanggal_inspeksi')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4"><label for="lokasi_inspeksi" class="form-label">Lokasi Inspeksi <span
                class="text-danger">*</span></label><input id="lokasi_inspeksi" name="lokasi_inspeksi"
            class="form-control @error('lokasi_inspeksi') is-invalid @enderror"
            value="{{ old('lokasi_inspeksi', $icc->lokasi_inspeksi ?? '') }}" required>
        @error('lokasi_inspeksi')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12 mt-4">
        <h6 class="text-primary fw-bold border-bottom pb-2">Item Pemeriksaan</h6>
    </div>
    <div class="col-12 table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Item Pemeriksaan</th>
                    <th class="text-center">Iya</th>
                    <th class="text-center">Tidak</th>
                    <th>Catatan</th>
                    <th>Tindakan perbaikan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($checklistItems as $number => $name)
                    @php $item = $itemsByName->get($name, ['status' => '', 'keterangan' => '', 'tindakan' => '']); @endphp
                    <tr>
                        <td class="text-center">{{ $number }}</td>
                        <td>{{ $name }}<input type="hidden" name="item_pemeriksaan[{{ $loop->index }}][no]"
                                value="{{ $number }}"><input type="hidden"
                                name="item_pemeriksaan[{{ $loop->index }}][nama]" value="{{ $name }}">
                        </td>
                        @foreach (['iya' => 'Iya', 'tidak' => 'Tidak'] as $status => $label)
                            <td class="text-center"><input type="radio" class="form-check-input"
                                    name="item_pemeriksaan[{{ $loop->parent->index }}][status]"
                                    value="{{ $status }}" @checked(($item['status'] ?? '') === $status) required
                                    aria-label="{{ $label }}"></td>
                        @endforeach
                        <td><input name="item_pemeriksaan[{{ $loop->index }}][keterangan]"
                                class="form-control form-control-sm" value="{{ $item['keterangan'] ?? '' }}"
                                placeholder="Catatan"></td>
                        <td><input name="item_pemeriksaan[{{ $loop->index }}][tindakan]"
                                class="form-control form-control-sm" value="{{ $item['tindakan'] ?? '' }}"
                                placeholder="Tindakan perbaikan"></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-12"><label for="note" class="form-label">Catatan Tambahan</label>
        <textarea id="note" name="note" class="form-control" rows="3">{{ old('note', $icc->note ?? '') }}</textarea>
    </div>
    @include('Inspeksi.partials.photo-upload')
</div>
<script>
    const unitSearchInput = document.querySelector('[data-unit-select2]');
    const unitSearchDropdown = document.getElementById('unitSearchDropdown');
    const unitSearchResults = document.getElementById('unitSearchResults');

    if (unitSearchInput && unitSearchDropdown && unitSearchResults) {
        let timer = null;

        const renderEmpty = () => {
            unitSearchResults.innerHTML = '<div class="list-group-item text-muted small">Tidak ada data unit</div>';
        };

        const runSearch = (keyword = '') => {
            const q = encodeURIComponent(keyword.trim());

            fetch(`/api/icc/units/select2?q=${q}`, {
                    headers: {
                        Accept: 'application/json',
                    },
                })
                .then((response) => response.json())
                .then((payload) => {
                    const results = payload.results ?? [];

                    unitSearchResults.innerHTML = '';

                    if (!results.length) {
                        renderEmpty();
                        return;
                    }

                    results.forEach((unit) => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'list-group-item list-group-item-action';
                        item.innerHTML =
                            `<span class="fw-semibold">${unit.code_unit ?? unit.text}</span><span class="d-block small text-muted">${unit.text}</span>`;
                        item.addEventListener('click', () => {
                            if (unitSearchInput) {
                                unitSearchInput.value = unit.code_unit ?? unit.code_unit ?? '';
                            }

                            unitSearchDropdown.style.display = 'none';
                        });

                        unitSearchResults.appendChild(item);
                    });
                })
                .catch(() => {
                    renderEmpty();
                });
        };

        unitSearchInput.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(() => runSearch(unitSearchInput.value), 250);

            if (unitSearchInput.value.trim().length > 0) {
                unitSearchDropdown.style.display = 'block';
            } else {
                unitSearchDropdown.style.display = 'none';
            }
        });

        unitSearchInput.addEventListener('focus', () => {
            if (unitSearchInput.value.trim().length > 0) {
                runSearch(unitSearchInput.value);
                unitSearchDropdown.style.display = 'block';
            }
        });

        document.addEventListener('click', (event) => {
            if (!unitSearchDropdown.contains(event.target) && event.target !== unitSearchInput) {
                unitSearchDropdown.style.display = 'none';
            }
        });
    }
</script>
