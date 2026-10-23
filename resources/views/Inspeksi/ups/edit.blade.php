@extends('layouts.app')
@section('judul', 'Edit Inspeksi UPS')
@section('subjudul', 'Form Ubah Data Inspeksi UPS')

@section('konten')
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Ubah Data Inspeksi UPS</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('inspeksi.ups.update', $ups) }}" enctype="multipart/form-data" method="POST">@csrf
                @method('PUT') @include('Inspeksi.ups._form')<div class="text-end mt-4"><a
                        href="{{ route('inspeksi.ups.index') }}" class="btn btn-light me-2">Batal</a><button
                        type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Perbarui Data</button></div>
            </form>
        </div>
    </div>
    @push('scripts')
        <script src="{{ asset('asset/js/app.js') }}"></script>
    @endpush
@endsection
