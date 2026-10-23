@php
    $inspectionItems = [
        'casing' => ['Kondisi Casing UPS', 'tindakan_casing'],
        'kebersihan' => ['Kebersihan UPS', 'tindakan_kebersihan'],
        'kabel_adaptor' => ['Kondisi kabel adaptor', 'tindakan_kabel_adaptor'],
        'tombol_switch' => ['Kondisi tombol dan switch', 'tindakan_tombol_switch'],
        'indikator_status' => ['Indikator status (power, battery, load)', 'tindakan_indikator_status'],
        'fungsi_alarm' => ['Fungsi alarm', 'tindakan_fungsi_alarm'],
        'respon_kehilangan_daya' => ['Respon terhadap kehilangan daya', 'tindakan_respon_kehilangan_daya'],
        'fuse' => ['Fuse (sekering)', 'tindakan_fuse'],
    ];
    $deviceLabels = [
        'nomor_aset' => 'Nomor Aset',
        'merek' => 'Merek',
        'tipe' => 'Tipe / Model',
        'serial_number' => 'Serial Number (SN)',
        'departemen' => 'Departemen',
        'lokasi' => 'Lokasi',
    ];
@endphp

@isset($asset)
    <input type="hidden" name="asset_id" value="{{ $asset->id }}">
@endisset

<div class="row g-3">
    <div class="col-12">
        <h6 class="text-primary fw-bold border-bottom pb-2">Informasi Perangkat</h6>
    </div>
    @foreach ($deviceLabels as $key => $label)
        <div class="col-md-6 col-lg-4">
            <label class="form-label">{{ $label }}</label>
            <input type="text" class="form-control" value="{{ $deviceInfo[$key] ?? '-' }}" readonly tabindex="-1">
        </div>
    @endforeach
    <div class="col-md-3">
        <label for="tanggal_inspeksi" class="form-label">Tanggal Inspeksi</label>
        <input id="tanggal_inspeksi" type="date" name="tanggal_inspeksi"
            class="form-control @error('tanggal_inspeksi') is-invalid @enderror"
            value="{{ old('tanggal_inspeksi', isset($ups) && $ups->tanggal_inspeksi ? substr($ups->tanggal_inspeksi, 0, 10) : now()->format('Y-m-d')) }}">
        @error('tanggal_inspeksi')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 mt-4">
        <h6 class="text-primary fw-bold border-bottom pb-2">Pemeriksaan Kondisi</h6>
    </div>
    <div class="col-12 table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Media yang Diperiksa</th>
                    <th class="text-center">Baik</th>
                    <th class="text-center">Tidak</th>
                    <th>Tindakan Perbaikan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($inspectionItems as $field => [$label, $actionField])
                    <tr>
                        <td class="fw-semibold">{{ $label }}</td>
                        <td class="text-center"><input class="form-check-input" type="radio"
                                name="{{ $field }}" value="baik" @checked(old($field, $ups->$field ?? '') === 'baik')></td>
                        <td class="text-center"><input class="form-check-input" type="radio"
                                name="{{ $field }}" value="tidak" @checked(old($field, $ups->$field ?? '') === 'tidak')></td>
                        <td>
                            <textarea name="{{ $actionField }}" class="form-control form-control-sm" rows="2"
                                placeholder="Keterangan / tindakan jika ada masalah">{{ old($actionField, $ups->$actionField ?? '') }}</textarea>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="col-12">
        <label for="keterangan" class="form-label">Keterangan / Remark</label>
        <textarea id="keterangan" name="keterangan" class="form-control @error('keterangan') is-invalid @enderror"
            rows="3" placeholder="Keterangan tambahan">{{ old('keterangan', $ups->keterangan ?? '') }}</textarea>
        @error('keterangan')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    @include('Inspeksi.partials.photo-upload')

    @push('style')
        <link rel="stylesheet" href="{{ asset('asset/css/photo.css') }}">
    @endpush
</div>
