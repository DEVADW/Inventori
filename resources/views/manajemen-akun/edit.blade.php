@extends('layouts.app')

@section('judul', 'Kelola Akses Akun')
@section('subjudul', 'Atur modul inspeksi untuk ' . $user->nama)

@section('konten')
    <div class="mb-3">
        <a href="{{ route('manajemen-akun.index') }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h6 class="fw-bold mb-1">{{ $user->nama }}</h6>
            <div class="text-muted small">NRP: {{ $user->nrp }} | Jabatan: {{ $user->jabatan ?? '-' }} | Role: {{ $user->role }}</div>
        </div>
    </div>

    <form action="{{ route('manajemen-akun.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong><i class="bi bi-exclamation-circle me-2"></i>Ada kesalahan!</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($user->role === 'admin')
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>Akun dengan role admin otomatis memiliki akses ke semua modul inspeksi.
            </div>
        @else
            <h6 class="fw-semibold mb-3">Pilih modul inspeksi yang dapat diakses:</h6>
            <div class="row">
                @foreach ($modules as $key => $module)
                    <div class="col-md-6 col-lg-4 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="modules[]" value="{{ $key }}"
                                id="module_{{ $key }}" {{ in_array($key, $selected, true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="module_{{ $key }}">
                                {{ $module['label'] }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <hr class="my-4">

        <h6 class="fw-semibold mb-1">Pengaturan Foto Inspeksi</h6>
        <p class="text-muted small mb-3">Atur apakah pengguna ini boleh memilih foto dari galeri, atau hanya dapat mengambil foto langsung dari kamera.</p>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" name="izin_galeri_foto" value="1"
                id="izin_galeri_foto" {{ old('izin_galeri_foto', $user->izin_galeri_foto) ? 'checked' : '' }}>
            <label class="form-check-label" for="izin_galeri_foto">
                Izinkan pilih foto dari galeri (selain kamera)
            </label>
        </div>
        <div class="form-text">Jika dimatikan, pengguna hanya bisa mengambil foto langsung dari kamera saat inspeksi.</div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
            </button>
            <a href="{{ route('manajemen-akun.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
@endsection