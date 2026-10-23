@extends('layouts.app')
@section('judul', 'Edit Data Inventori Proyektor')
@section('subjudul', 'Form Edit Data Inventori Proyektor')

@section('konten')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 fw-bold">Ubah Data Inventori Proyektor</h5>
        <a href="{{ route('aset.inventoriproyektor.index') }}" class="btn btn-sm btn-outline-secondary"><i
                class="bi bi-arrow-left me-1"></i>Kembali</a>
    </div>

    <form action="{{ route('aset.inventoriproyektor.update', $inventoriproyektor) }}" method="POST">
        @csrf
        @method('PUT')
        @include('aset.inventoriproyektor._form')
        <div class="text-end mt-4">
            <a href="{{ route('aset.inventoriproyektor.index') }}" class="btn btn-light me-2">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Data</button>
        </div>
    </form>
@endsection
