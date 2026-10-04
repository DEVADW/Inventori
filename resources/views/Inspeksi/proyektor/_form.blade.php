@php

    $deviceFields = [
        'nomor_aset' => ['Nomor Aset', 'Contoh: AST-PRJ-001'],
        'merek' => ['Merek', 'Contoh: Epson'],
        'type' => ['Tipe / Model', 'Masukkan tipe/model'],
        'sn' => ['Serial Number (SN)', 'Masukkan serial number'],
        'departemen' => ['Departemen', 'Contoh: ICT'],
        'lokasi' => ['Lokasi', 'Contoh: Ruang Rapat'],
    ];

    $inspectionItems = [
        'kondisi_casing' => ['Kondisi casing', 'tindakan_kondisi_casing'],
        'kebersihan' => ['Kebersihan perangkat', 'tindakan_kebersihan'],
        'kabel_adaptor' => ['Kabel adaptor', 'tindakan_kabel_adaptor'],
        'lensa_proyektor' => ['Lensa proyektor', 'tindakan_lensa_proyektor'],
        'indikator_lampu' => ['Indikator lampu', 'tindakan_indikator_lampu'],
        'fokus_zoom' => ['Fokus dan zoom', 'tindakan_fokus_zoom'],
        'kecerahan_kontras' => ['Kecerahan dan kontras', 'tindakan_kecerahan_kontras'],
        'koneksi_input_hdmi' => ['Koneksi input HDMI', null],
        'koneksi_input_vga' => ['Koneksi input VGA', null],
        'koneksi_input_usb' => ['Koneksi input USB', null],
    ];
@endphp
<div class="row g-3">
    <div class="col-12">
        <h6 class="text-primary fw-bold border-bottom pb-2">Informasi Perangkat</h6>
    </div>
    @foreach ($deviceFields as $field => [$label, $placeholder])
        <div class="col-md-6 col-lg-4"><label for="{{ $field }}"
                class="form-label">{{ $label }}</label><input id="{{ $field }}" name="{{ $field }}"
                type="text" class="form-control @error($field) is-invalid @enderror"
                value="{{ old($field, $proyektor->$field ?? '') }}" placeholder="{{ $placeholder }}">
            @error($field)
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    @endforeach
    <div class="col-md-3"><label for="tanggal_inspeksi" class="form-label">Tanggal Inspeksi</label><input
            id="tanggal_inspeksi" type="date" name="tanggal_inspeksi" class="form-control"
            value="{{ old('tanggal_inspeksi', isset($proyektor) && $proyektor->tanggal_inspeksi ? substr($proyektor->tanggal_inspeksi, 0, 10) : now()->format('Y-m-d')) }}">
    </div>
    <div class="col-12 mt-4">
        <h6 class="text-primary fw-bold border-bottom pb-2">Pemeriksaan Kondisi</h6>
    </div>
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
                    <td class="text-center"><input class="form-check-input" type="radio" name="{{ $field }}"
                            value="baik" @checked(old($field, $proyektor->$field ?? '') === 'baik')></td>
                    <td class="text-center"><input class="form-check-input" type="radio" name="{{ $field }}"
                            value="tidak" @checked(old($field, $proyektor->$field ?? '') === 'tidak')></td>
                    <td>
                        @if ($actionField)
                        <textarea name="{{ $actionField }}" class="form-control form-control-sm" rows="2"
                            placeholder="Keterangan / tindakan jika ada masalah">{{ old($actionField, $proyektor->$actionField ?? '') }}</textarea>@else<span class="text-muted small">-</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="col-12"><label for="keterangan" class="form-label">Keterangan</label>
    <textarea id="keterangan" name="keterangan" class="form-control" rows="3" placeholder="Keterangan tambahan">{{ old('keterangan', $proyektor->keterangan ?? '') }}</textarea>
</div>

@include('Inspeksi.partials.photo-upload')
</div>
