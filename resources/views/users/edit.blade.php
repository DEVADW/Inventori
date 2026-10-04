@extends('layouts.app')
@section('judul', 'Edit Pengguna')
@section('subjudul', 'Edit Data Pengguna')

@section('konten')
    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('users._form')

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
            </button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Batal
            </a>
        </div>
    </form>
@endsection
