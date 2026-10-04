@extends('layouts.app')
@section('judul', 'Edit Inspeksi UPS')
@section('subjudul', 'Form Ubah Data Inspeksi UPS')

@section('konten')
    @php
        $items = [
            'casing' => ['label' => 'Kondisi Casing UPS', 'tindakan' => 'tindakan_casing'],
            'kebersihan' => ['label' => 'Kebersihan UPS', 'tindakan' => 'tindakan_kebersihan'],
            'kabel_adaptor' => ['label' => 'Kondisi kabel adaptor', 'tindakan' => 'tindakan_kabel_adaptor'],
            'tombol_switch' => ['label' => 'Kondisi tombol dan switch', 'tindakan' => 'tindakan_tombol_switch'],
            'indikator_status' => [
                'label' => 'Indikator status (power, battery, load)',
                'tindakan' => 'tindakan_indikator_status',
            ],
            'fungsi_alarm' => ['label' => 'Fungsi alarm', 'tindakan' => 'tindakan_fungsi_alarm'],
            'respon_kehilangan_daya' => [
                'label' => 'Respon terhadap kehilangan daya',
                'tindakan' => 'tindakan_respon_kehilangan_daya',
            ],
            'fuse' => ['label' => 'Fuse (sekering)', 'tindakan' => 'tindakan_fuse'],
        ];
    @endphp

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <div class="d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold">Ubah Data Inspeksi UPS</h5>
                <a href="{{ route('inspeksi.ups.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('inspeksi.ups.update', $ups->id) }}" enctype="multipart/form-data" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-12">
                        <h6 class="text-primary fw-bold border-bottom pb-2">Informasi Perangkat</h6>
                    </div>

                    @foreach ([
            'nomor_aset' => ['Nomor Aset', 'Contoh: AST-UPS-001', true],
            'merek' => ['Merek', 'Contoh: APC, ICA', true],
            'type' => ['Tipe / Model', 'Contoh: Smart-UPS 1500VA', false],
            'sn' => ['Serial Number (SN)', 'Masukkan Serial Number', false],
            'departemen' => ['Departemen', 'Contoh: IT, HRD, Finance', true],
            'lokasi' => ['Lokasi', 'Contoh: Ruang Server Lt. 2', true],
        ] as $field => [$label, $placeholder, $required])
                        <div class="col-md-6 col-lg-4">
                            <label for="{{ $field }}"
                                class="form-label {{ $required ? 'required' : '' }}">{{ $label }}</label>
                            <input type="text" class="form-control @error($field) is-invalid @enderror"
                                id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $ups->$field) }}"
                                placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }}>
                            @error($field)
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endforeach
                    <div class="col-md-4">
                        <label for="tanggal_inspeksi" class="form-label required">Tanggal Inspeksi</label>
                        <input type="date" class="form-control @error('tanggal_inspeksi') is-invalid @enderror"
                            id="tanggal_inspeksi" name="tanggal_inspeksi"
                            value="{{ old('tanggal_inspeksi', $ups->tanggal_inspeksi ? substr($ups->tanggal_inspeksi, 0, 10) : '') }}"
                            required>
                        @error('tanggal_inspeksi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="table-responsive my-4">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Media yang Diperiksa</th>
                                    <th style="width: 100px;" class="text-center">Baik</th>
                                    <th style="width: 100px;" class="text-center">Tidak</th>
                                    <th style="width: 35%;">Tindakan Perbaikan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $field => $item)
                                    <tr>
                                        <td><span class="fw-semibold">{{ $item['label'] }}</span></td>
                                        <td class="text-center"><input class="form-check-input" type="radio"
                                                name="{{ $field }}" value="baik"
                                                {{ old($field, $ups->$field) === 'baik' ? 'checked' : '' }}></td>
                                        <td class="text-center"><input class="form-check-input" type="radio"
                                                name="{{ $field }}" value="tidak"
                                                {{ old($field, $ups->$field) === 'tidak' ? 'checked' : '' }}></td>
                                        <td>
                                            <textarea name="{{ $item['tindakan'] }}" class="form-control form-control-sm" rows="2"
                                                placeholder="Keterangan / tindakan jika ada masalah">{{ old($item['tindakan'], $ups->{$item['tindakan']}) }}</textarea>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div>
                            <label for="keterangan" class="form-label required">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" class="form-control" @error('keterangan') @enderror
                                value="{{ old('keterangan', $ups->keterangan ? $ups->keterangan : '-') }}" placeholder="Isi Keterangan"
                                style="height: 100px;" required>
                            </textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    @include('Inspeksi.partials.photo-upload')
                    <div class="col-12 text-end mt-4">
                        <button type="reset" class="btn btn-light me-2">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Update
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
    @push('scripts')<script src="{{ asset('asset/js/app.js') }}"></script>@endpush
@endsection
