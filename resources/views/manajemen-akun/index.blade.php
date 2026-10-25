@extends('layouts.app')

@section('judul', 'Manajemen Akun')
@section('subjudul', 'Kelola Hak Akses Akun Pengguna')

@section('konten')
    <div class="card-header mb-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <div>
                <h5 class="mb-0">Daftar Akun</h5>
                <small class="text-muted">Atur modul inspeksi yang dapat diakses oleh setiap akun.</small>
            </div>
            <div class="d-flex gap-2">
                <x-data-search :action="route('manajemen-akun.index')" placeholder="Cari nama, NRP, jabatan..."></x-data-search>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px" class="text-center">No</th>
                    <th>Nama</th>
                    <th>NRP</th>
                    <th>Jabatan</th>
                    <th class="text-center">Role</th>
                    <th>Akses Inspeksi</th>
                    <th class="text-center">Akses Foto</th>
                    <th style="width: 120px" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td class="text-center">{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $user->foto ? asset('storage/' . $user->foto) : asset('images/default-avatar.png') }}"
                                    alt="{{ $user->nama }}" class="rounded-circle object-fit-cover"
                                    style="width: 32px; height: 32px;">
                                <div class="fw-semibold">{{ $user->nama }}</div>
                            </div>
                        </td>
                        <td>{{ $user->nrp }}</td>
                        <td>{{ $user->jabatan ?? '-' }}</td>
                        <td class="text-center">
                            @if ($user->role === 'admin')
                                <span class="badge bg-success"><i class="bi bi-shield-check me-1"></i>Admin</span>
                            @else
                                <span class="badge bg-info"><i class="bi bi-person me-1"></i>User</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $allowed = $user->allowedInspectionModules();
                                $moduleLabels = \App\Models\User::inspectionModules();
                            @endphp
                            @if ($user->role === 'admin')
                                <span class="badge bg-primary">Semua modul</span>
                            @elseif (empty($allowed))
                                <span class="text-muted small">Tidak ada akses</span>
                            @else
                                @foreach ($allowed as $module)
                                    <span class="badge bg-secondary me-1">{{ $moduleLabels[$module]['label'] ?? $module }}</span>
                                @endforeach
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($user->izin_galeri_foto)
                                <span class="badge bg-info"><i class="bi bi-images me-1"></i>Kamera + Galeri</span>
                            @else
                                <span class="badge bg-secondary"><i class="bi bi-camera-fill me-1"></i>Kamera Saja</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('manajemen-akun.edit', $user->id) }}" class="btn btn-sm btn-warning text-white" title="Kelola Akses">
                                <i class="bi bi-key"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-5 me-2"></i>Tidak ada data akun
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
@endsection