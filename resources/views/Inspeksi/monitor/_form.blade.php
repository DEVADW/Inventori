@php
    $deviceFields = [
        'nomor_aset' => ['Nomor Aset', 'Contoh: AST-MON-001'],
        'merek' => ['Merek', 'Contoh: LG'],
        'type' => ['Tipe / Model', 'Masukkan tipe/model'],
        'sn' => ['Serial Number (SN)', 'Masukkan serial number'],
        'departemen' => ['Departemen', 'Contoh: ICT'],
        'lokasi' => ['Lokasi', 'Contoh: Ruang Rapat'],
    ];
    $inspectionItems = [
        'tampilan_layer' => ['Tampilan layar', 'tindakan_tampilan_layer'],
        'kabel_power' => ['Kabel power', 'tindakan_kabel_power'],
        'bracket_dudukan' => ['Bracket / dudukan', 'tindakan_bracket_dudukan'],
        'kebersihan' => ['Kebersihan perangkat', 'tindakan_kebersihan'],
        'stop_kontak' => ['Stop kontak', 'tindakan_stop_kontak'],
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
                value="{{ old($field, $monitor->$field ?? '') }}" placeholder="{{ $placeholder }}">
            @error($field)
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    @endforeach
    <div class="col-md-3"><label for="tanggal_inspeksi" class="form-label">Tanggal Inspeksi</label><input
            id="tanggal_inspeksi" type="date" name="tanggal_inspeksi" class="form-control"
            value="{{ old('tanggal_inspeksi', isset($monitor) && $monitor->tanggal_inspeksi ? substr($monitor->tanggal_inspeksi, 0, 10) : now()->format('Y-m-d')) }}">
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
                                name="{{ $field }}" value="baik" @checked(old($field, $monitor->$field ?? '') === 'baik')></td>
                        <td class="text-center"><input class="form-check-input" type="radio"
                                name="{{ $field }}" value="tidak" @checked(old($field, $monitor->$field ?? '') === 'tidak')></td>
                        <td>
                            <textarea name="{{ $actionField }}" class="form-control form-control-sm" rows="2"
                                placeholder="Keterangan / tindakan jika ada masalah">{{ old($actionField, $monitor->$actionField ?? '') }}</textarea>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-12"><label for="keterangan" class="form-label">Keterangan</label>
        <textarea id="keterangan" name="keterangan" class="form-control" rows="3" placeholder="Keterangan tambahan">{{ old('keterangan', $monitor->keterangan ?? '') }}</textarea>
    </div>



    @include('Inspeksi.partials.photo-upload')
</div>
