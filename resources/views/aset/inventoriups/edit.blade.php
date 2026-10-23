@extends('layouts.app')
@section('judul', 'Edit Data Inventori UPS')
@section('subjudul', 'Form Edit Data Inventori UPS')

@section('konten')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 fw-bold">Ubah Data Inventori UPS</h5>
        <a href="{{ route('aset.inventoriups.index') }}" class="btn btn-sm btn-outline-secondary"><i
                class="bi bi-arrow-left me-1"></i>Kembali</a>
    </div>

    <form action="{{ route('aset.inventoriups.update', $inventoriups) }}" method="POST">
        @csrf
        @method('PUT')
        @include('aset.inventoriups._form')
        <div class="text-end mt-4">
            <a href="{{ route('aset.inventoriups.index') }}" class="btn btn-light me-2">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Data</button>
        </div>
    </form>
@endsection
