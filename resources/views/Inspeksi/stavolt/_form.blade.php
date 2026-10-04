@php
    $inspectionItems = [
        'casing' => ['Kondisi casing Stavolt', 'tindakan_casing'],
        'kebersihan' => ['Kebersihan Stavolt', 'tindakan_kebersihan'],
        'kabel_adaptor' => ['Kondisi kabel/adaptor', 'tindakan_kabel_adaptor'],
        'tombol_switch' => ['Kondisi tombol dan switch', 'tindakan_tombol_switch'],
        'indikator_voltase' => ['Indikator voltase', 'tindakan_indikator_voltase'],
        'respon_perubahan_beban' => ['Respons terhadap perubahan beban', 'tindakan_respon_perubahan_beban'],
    ];
    $deviceFields = [
        'nomor_aset' => ['Nomor Aset', 'Contoh: AST-STV-001'],
        'merek' => ['Merek', 'Contoh: Matsunaga'],
        'type' => ['Tipe / Model', 'Masukkan tipe/model'],
        'sn' => ['Serial Number (SN)', 'Masukkan serial number'],
        'departemen' => ['Departemen', 'Contoh: ICT'],
        'lokasi' => ['Lokasi', 'Contoh: Ruang Server'],
    ];
@endphp

<div class="row g-3">
    <div class="col-12">
        <h6 class="text-primary fw-bold border-bottom pb-2">Informasi Perangkat</h6>
    </div>
    @foreach ($deviceFields as $field => [$label, $placeholder])
        <div class="col-md-6 col-lg-4">
            <label for="{{ $field }}" class="form-label">{{ $label }}</label>
            <input id="{{ $field }}" name="{{ $field }}" type="text"
                class="form-control @error($field) is-invalid @enderror"
                value="{{ old($field, $stavolt->$field ?? '') }}" placeholder="{{ $placeholder }}">
            @error($field)
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    @endforeach
    <div class="col-md-3">
        <label for="tanggal_inspeksi" class="form-label">Tanggal Inspeksi</label>
        <input id="tanggal_inspeksi" type="date" name="tanggal_inspeksi"
            class="form-control @error('tanggal_inspeksi') is-invalid @enderror"
            value="{{ old('tanggal_inspeksi', isset($stavolt) && $stavolt->tanggal_inspeksi ? substr($stavolt->tanggal_inspeksi, 0, 10) : now()->format('Y-m-d')) }}">
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
                                name="{{ $field }}" value="baik" @checked(old($field, $stavolt->$field ?? '') === 'baik')></td>
                        <td class="text-center"><input class="form-check-input" type="radio"
                                name="{{ $field }}" value="tidak" @checked(old($field, $stavolt->$field ?? '') === 'tidak')></td>
                        <td>
                            <textarea name="{{ $actionField }}" class="form-control form-control-sm" rows="2"
                                placeholder="Keterangan / tindakan jika ada masalah">{{ old($actionField, $stavolt->$actionField ?? '') }}</textarea>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="col-12">
        <label for="keterangan" class="form-label">Keterangan</label>
        <textarea id="keterangan" name="keterangan" class="form-control @error('keterangan') is-invalid @enderror"
            rows="3" placeholder="Keterangan tambahan">{{ old('keterangan', $stavolt->keterangan ?? '') }}</textarea>
        @error('keterangan')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>



    @include('Inspeksi.partials.photo-upload')

    @push('style')
        <link rel="stylesheet" href="{{ asset('asset/css/photo.css') }}">
    @endpush
</div>
