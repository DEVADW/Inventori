@extends('layouts.app')

@section('judul', 'Tambah Inspeksi Stavolt')
@section('subjudul', 'Form Tambah Data Inspeksi Stavolt')

@section('konten')
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Input Data Inspeksi Stavolt</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('inspeksi.stavolt.store') }}" enctype="multipart/form-data" method="POST">@csrf @include('Inspeksi.stavolt._form')<div
                    class="text-end mt-4"><button type="reset" class="btn btn-light me-2">Reset</button><button
                        type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Data</button></div>
            </form>
        </div>
    </div>
    @push('scripts')
    <script src="{{ asset('asset/js/app.js') }}"></script>
    @endpush
@endsection