@extends('layouts.app')
@section('judul', 'Tambah Inspeksi UPS')
@section('subjudul', 'Form Tambah Data Inspeksi UPS')

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
                <h5 class="card-title mb-0 fw-bold">Input Data Inspeksi UPS</h5>
                <a href="{{ route('inspeksi.ups.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('inspeksi.ups.store') }}" enctype="multipart/form-data" method="POST">
                @csrf

                <div class="row g-3">
                    {{-- Informasi Aset --}}
                    <div class="col-12">
                        <h6 class="text-primary fw-bold border-bottom pb-2">Informasi Perangkat</h6>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <label for="nomor_aset" class="form-label required">Nomor Aset</label>
                        <input type="text" class="form-control @error('nomor_aset') is-invalid @enderror" id="nomor_aset"
                            name="nomor_aset" value="{{ old('nomor_aset') }}" placeholder="Contoh: AST-UPS-001" required>
                        @error('nomor_aset')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <label for="merek" class="form-label required">Merek</label>
                        <input type="text" class="form-control @error('merek') is-invalid @enderror" id="merek"
                            name="merek" value="{{ old('merek') }}" placeholder="Contoh: APC, ICA" required>
                        @error('merek')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <label for="type" class="form-label">Tipe / Model</label>
                        <input type="text" class="form-control @error('type') is-invalid @enderror" id="type"
                            name="type" value="{{ old('type') }}" placeholder="Contoh: Smart-UPS 1500VA">
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <label for="sn" class="form-label">Serial Number (SN)</label>
                        <input type="text" class="form-control @error('sn') is-invalid @enderror" id="sn"
                            name="sn" value="{{ old('sn') }}" placeholder="Masukkan Serial Number">
                        @error('sn')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <label for="departemen" class="form-label required">Departemen</label>
                        <input type="text" class="form-control @error('departemen') is-invalid @enderror" id="departemen"
                            name="departemen" value="{{ old('departemen') }}" placeholder="Contoh: IT, HRD, Finance"
                            required>
                        @error('departemen')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <label for="lokasi" class="form-label required">Lokasi</label>
                        <input type="text" class="form-control @error('lokasi') is-invalid @enderror" id="lokasi"
                            name="lokasi" value="{{ old('lokasi') }}" placeholder="Contoh: Ruang Server Lt. 2" required>
                        @error('lokasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="tanggal_inspeksi" class="form-label required">Tanggal Inspeksi</label>
                        <input type="date" class="form-control @error('tanggal_inspeksi') is-invalid @enderror"
                            id="tanggal_inspeksi" name="tanggal_inspeksi"
                            value="{{ old('tanggal_inspeksi', date('Y-m-d')) }}" required>
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
                                @foreach ($items as $key => $item)
                                    <tr>
                                        <td>
                                            <span class="fw-semibold">{{ $item['label'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input" type="radio" name="{{ $key }}"
                                                value="baik" {{ old($key) == 'baik' ? 'checked' : '' }} required>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input" type="radio" name="{{ $key }}"
                                                value="tidak" {{ old($key) == 'tidak' ? 'checked' : '' }} required>
                                        </td>
                                        <td>
                                            <textarea name="{{ $item['tindakan'] }}" class="form-control form-control-sm" rows="2"
                                                placeholder="Keterangan / tindakan jika ada masalah">{{ old($item['tindakan']) }}</textarea>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div>
                            <label for="keterangan" class="form-label required">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" class="form-control" @error('keterangan') @enderror
                                value="{{ old('keterangan') }}" placeholder="Isi Keterangan">
                                </textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    @include('Inspeksi.partials.photo-upload')
                    {{-- Submit Buttons --}}
                    <div class="col-12 text-end mt-4">
                        <button type="reset" class="btn btn-light me-2">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan Data
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @push('scripts')<script src="{{ asset('asset/js/app.js') }}"></script>@endpush
@endsection
