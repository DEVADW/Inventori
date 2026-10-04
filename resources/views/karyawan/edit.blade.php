@extends('layouts.app')
@section('judul', 'Edit Data Karyawan')
@section('subjudul', 'Form Edit Data Karyawan')
@section('konten')
   <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0 fw-bold"> Ubah Data Karyawan </h5>
      <a href="{{ route('karyawan.index') }}" class="btn btn-sm btn-outline-secondary"><i
            class="bi bi-arrow-left me-1"></i>Kembali</a>

      </h5>
   </div>

   <form action="{{ route('karyawan.update', $karyawan) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      @include('karyawan._form')
      <div class="text-end mt-4"><a href="{{ route('karyawan.index') }}" class="btn btn-light me-2">Batal</a><button
            type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Data</button></div>
   </form>
@endsection