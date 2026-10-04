@extends('layouts.app')
@section('judul', 'Edit Pemeliharaan ICC')
@section('subjudul', 'Perbarui formulir pemeliharaan perangkat ICC')
@section('konten')
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('inspeksi.icc.update', $icc) }}" enctype="multipart/form-data" method="POST">@csrf @method('PUT')
                @include('Inspeksi.icc._form_fields')<div class="text-end mt-4"><a href="{{ route('inspeksi.icc.index') }}"
                        class="btn btn-light me-2">Batal</a><button class="btn btn-primary"><i
                            class="bi bi-save me-1"></i>Perbarui Data</button></div>
            </form>
            @push('scripts')<script src="{{ asset('asset/js/app.js') }}"></script>@endpush
        </div>
    </div>
@endsection
