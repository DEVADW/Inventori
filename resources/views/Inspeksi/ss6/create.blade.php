@extends('layouts.app')

@section('judul', 'Form Inspeksi ')
@section('subjudul', 'Form Inspeksi SS6')

@section('konten')

<div class="card shadow-sm">

    <div class="card-body">

        <form action="{{ route('inspeksi.ss6.store') }}" enctype="multipart/form-data" method="POST">
            @csrf
            @include('Inspeksi.ss6._form')
            <hr>

            <button class="btn btn-success">
                Simpan Data
            </button>

            <a href="{{ route('inspeksi.ss6.index') }}"
                class="btn btn-secondary">
                Kembali
            </a>

        </form>
        @push('scripts')<script src="{{ asset('asset/js/app.js') }}"></script>@endpush

    </div>

</div>

@endsection