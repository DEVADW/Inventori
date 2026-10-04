@extends('layouts.app')
@section('judul', 'Tambah Inventaris')
@section('subjudul', 'Form Tambah Data Inventaris')

@section('konten')
    <form action="{{ route('inventaris.store') }}" method="POST">
        @csrf
        <div class="card-body">
            {{-- Nama --}}
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama"
                    value="{{ old('nama') }}" placeholder="Masukkan nama peminjam" required>
                @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- NRP --}}
            <div class="mb-3">
                <label for="nrp" class="form-label">NRP <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nrp') is-invalid @enderror" id="nrp" name="nrp"
                    value="{{ old('nrp') }}" placeholder="Masukkan NRP" required>
                @error('nrp')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Nama Perangkat --}}
            <div class="mb-3">
                <label for="nama_perangkat" class="form-label">Nama Perangkat <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nama_perangkat') is-invalid @enderror" id="nama_perangkat"
                    name="nama_perangkat" value="{{ old('nama_perangkat') }}" placeholder="Contoh: Laptop / Monitor"
                    required>
                @error('nama_perangkat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- No Asset --}}
            <div class="mb-3">
                <label for="no_asset" class="form-label">No. Asset <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('no_asset') is-invalid @enderror" id="no_asset"
                    name="no_asset" value="{{ old('no_asset') }}" placeholder="Masukkan nomor asset" required>
                @error('no_asset')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Status Peminjaman --}}
            <div class="mb-3">
                <label for="status_peminjaman" class="form-label">Status Peminjaman <span
                        class="text-danger">*</span></label>
                <select class="form-select @error('status_peminjaman') is-invalid @enderror" id="status_peminjaman"
                    name="status_peminjaman" required>
                    <option value="" disabled selected>-- Pilih Status --</option>
                    <option value="Belum Dikembalikan" {{ old('status_peminjaman') == 'Belum Dikembalikan' ? 'selected' : '' }}>
                        Belum Dikembalikan</option>
                    <option value="Dikembalikan" {{ old('status_peminjaman') == 'Dikembalikan' ? 'selected' : '' }}>
                        Dikembalikan</option>
                </select>
                @error('status_peminjaman')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Tanggal Peminjaman --}}
            <div class="mb-3">
                <label for="tanggal_peminjaman" class="form-label">Tanggal Peminjaman <span
                        class="text-danger">*</span></label>
                <input type="date" class="form-control @error('tanggal_peminjaman') is-invalid @enderror"
                    id="tanggal_peminjaman" name="tanggal_peminjaman"
                    value="{{ old('tanggal_peminjaman', date('Y-m-d')) }}" required>
                @error('tanggal_peminjaman')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Tanggal Pengembalian --}}
            {{-- <div class="mb-3">
                <label for="tanggal_pengembalian" class="form-label">Tanggal Pengembalian</label>
                <input type="date" class="form-control @error('tanggal_pengembalian') is-invalid @enderror"
                    id="tanggal_pengembalian" name="tanggal_pengembalian" value="{{ old('tanggal_pengembalian') }}">
                @error('tanggal_pengembalian')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div> --}}
        </div>

        <div class="card-footer d-flex gap-2">
            <a href="{{ route('inventaris.index') }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <button type="submit" class="btn btn-sm btn-success">
                <i class="bi bi-save me-1"></i> Simpan Data
            </button>
            <a href="{{ route('inventaris.index') }}" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-x-circle me-1"></i> Batal
            </a>
        </div>
    </form>
@endsection
