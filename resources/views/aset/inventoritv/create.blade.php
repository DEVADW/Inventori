@extends('layouts.app')
@section('judul', 'Tambah Inventori TV')
@section('subjudul', 'Form Tambah Data Inventori TV')

@section('konten')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 fw-bold">Tambah Data Inventori TV</h5>
        <a href="{{ route('aset.inventoritv.index') }}" class="btn btn-sm btn-outline-secondary"><i
                class="bi bi-arrow-left me-1"></i>Kembali</a>
    </div>

    <form action="{{ route('aset.inventoritv.store') }}" method="POST">
        @csrf
        @include('aset.inventoritv._form')
        <div class="text-end mt-4">
            <a href="{{ route('aset.inventoritv.index') }}" class="btn btn-light me-2">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Data</button>
        </div>
    </form>
@endsection
