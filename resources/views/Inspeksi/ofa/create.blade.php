@extends('layouts.app')
@section('judul', 'Inspeksi Perangkat OFA')
@section('subjudul', 'Tambah Checklist Inspeksi Perangkat Onboard FleetSafe Assist')
@section('konten')
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-end mb-3">
                <span class="badge bg-info-subtle text-info-emphasis">Periode: {{ $periodLabel }}</span>
            </div>
            <form action="{{ route('inspeksi.ofa.store') }}" enctype="multipart/form-data" method="POST">@csrf @include('Inspeksi.ofa._form_fields')<div
                    class="text-end mt-4"><a href="{{ route('inspeksi.ofa.index') }}"
                        class="btn btn-light me-2">Batal</a><button class="btn btn-primary"><i
                            class="bi bi-save me-1"></i>Simpan Data</button></div>
            </form>
            @push('scripts')<script src="{{ asset('asset/js/app.js') }}"></script>@endpush
        </div>
    </div>
@endsection
