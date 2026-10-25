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

@isset($asset)
    <input type="hidden" name="asset_id" value="{{ $asset->id }}">
@endisset

<div class="row g-3">
    <div class="col-12">
        <h6 class="text-primary fw-bold border-bottom pb-2">Informasi Unit</h6>
    </div>
    <div class="col-md-4">
        <label class="form-label">No. Lambung Unit</label>
        <input type="text" class="form-control" value="{{ $deviceInfo['no_lambung_unit'] ?? '-' }}" readonly tabindex="-1">
    </div>
    @isset($asset)
        <div class="col-md-4">
            <x-sim-card-select :options="$availableSimCards" :selected="old('sim_card_ids', $selectedSimCardIds ?? [])" />
        </div>
    @endisset
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
