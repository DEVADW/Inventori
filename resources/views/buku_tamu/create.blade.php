@extends('layouts.app')
@section('judul', 'Tambah Tamu')
@section('subjudul', 'Form Tambah Data Tamu')

@section('konten')

    <form action="{{ route('tamu.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                {{-- Nama --}}
                <div class="col-md-6 mb-3">
                    <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                        name="nama" value="{{ old('nama') }}" placeholder="Masukkan nama tamu" required>
                    @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- No Telepon --}}
                <div class="col-md-6 mb-3">
                    <label for="no_telp" class="form-label">No. Telepon <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('no_telp') is-invalid @enderror" id="no_telp"
                        name="no_telp" value="{{ old('no_telp') }}" placeholder="Contoh: 081234567890" required>
                    @error('no_telp')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- NRP / NIK --}}
                <div class="col-md-6 mb-3">
                    <label for="nrp" class="form-label">NRP / NIK</label>
                    <input type="text" class="form-control @error('nrp') is-invalid @enderror" id="nrp"
                        name="nrp" value="{{ old('nrp') }}" placeholder="Masukkan NRP tamu">
                    @error('nrp')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Instansi --}}
                <div class="col-md-6 mb-3">
                    <label for="instansi" class="form-label">Instansi / Perusahaan <span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('instansi') is-invalid @enderror" id="instansi"
                        name="instansi" value="{{ old('instansi') }}" placeholder="Asal Instansi" required>
                    @error('instansi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Keperluan --}}
                <div class="col-12 mb-3">
                    <label for="keperluan" class="form-label">Keperluan <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('keperluan') is-invalid @enderror" id="keperluan" name="keperluan" rows="3"
                        placeholder="Jelaskan maksud dan tujuan kunjungan" required>{{ old('keperluan') }}</textarea>
                    @error('keperluan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="card-footer d-flex gap-2">
            <a href="{{ route('tamu.index') }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Simpan Data
            </button>
            <button type="reset" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
            </button>
        </div>
    </form>
@endsection
