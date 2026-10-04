@extends('layouts.app')

@section('judul', 'Edit Inspeksi Stavolt')
@section('subjudul', 'Form Ubah Data Inspeksi Stavolt')

@section('konten')
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Ubah Data Inspeksi Stavolt</h5>

        </div>
        <div class="card-body">
            <form action="{{ route('inspeksi.stavolt.update', $stavolt) }}" enctype="multipart/form-data" method="POST">@csrf
                @method('PUT') @include('Inspeksi.stavolt._form')<div class="text-end mt-4"><a
                        href="{{ route('inspeksi.stavolt.index') }}" class="btn btn-light me-2">Batal</a><button
                        type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Perbarui Data</button></div>
            </form>
        </div>
    </div>
    @push('scripts')
        <script src="{{ asset('asset/js/app.js') }}"></script>
    @endpush
@endsection
