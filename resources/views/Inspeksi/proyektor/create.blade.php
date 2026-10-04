@extends('layouts.app')
@section('judul', 'Tambah Inspeksi Proyektor')
@section('subjudul', 'Form Tambah Data Inspeksi Proyektor')
@section('konten')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 fw-bold">Input Data Inspeksi Proyektor</h5><a href="{{ route('inspeksi.proyektor.index') }}"
            class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    </div>
    <form action="{{ route('inspeksi.proyektor.store') }}" enctype="multipart/form-data" method="POST">@csrf @include('inspeksi.proyektor._form')
        <div class="text-end mt-4"><a href="{{ route('inspeksi.proyektor.index') }}"
                class="btn btn-light me-2">Batal</a><button type="submit" class="btn btn-primary"><i
                    class="bi bi-save me-1"></i>Simpan Data</button></div>
    </form>
    @push('scripts')<script src="{{ asset('asset/js/app.js') }}"></script>@endpush
@endsection