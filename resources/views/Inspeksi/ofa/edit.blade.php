@extends('layouts.app')
@section('judul', 'Inspeksi Perangkat OFA')
@section('subjudul', 'Ubah Checklist Inspeksi Perangkat Onboard FleetSafe Assist')
@section('konten')
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('inspeksi.ofa.update', $ofa) }}" enctype="multipart/form-data" method="POST">@csrf @method('PUT')
                @include('Inspeksi.ofa._form_fields')<div class="text-end mt-4"><a href="{{ route('inspeksi.ofa.index') }}"
                        class="btn btn-light me-2">Batal</a><button class="btn btn-primary"><i
                            class="bi bi-save me-1"></i>Perbarui Data</button></div>
            </form>
            @push('scripts')<script src="{{ asset('asset/js/app.js') }}"></script>@endpush
        </div>
    </div>
@endsection
