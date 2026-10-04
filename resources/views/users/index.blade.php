@extends('layouts.app')
@section('judul', 'Manajemen Pengguna')
@section('subjudul', 'Kelola Daftar Pengguna Sistem')

@section('konten')
    <div class="card-header mb-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <div class="row gap-2">
                <div class="col-auto">
                    <div class="card bg-primary-subtle mb-0" style="width: 12rem;">
                        <div class="card-body p-2 text-center">
                            <h6 class="card-title mb-1 small fw-bold text-uppercase">Total Pengguna</h6>
                            <h4 class="card-text fw-bold mb-0">{{ $totalUsers }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="card bg-success-subtle mb-0" style="width: 12rem;">
                        <div class="card-body p-2 text-center">
                            <h6 class="card-title mb-1 small fw-bold text-uppercase">Admin</h6>
                            <h4 class="card-text fw-bold mb-0">{{ $adminCount }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column flex-sm-row align-items-end align-items-sm-center gap-2">
                <x-data-search :action="route('users.index')" placeholder="Cari nama, NRP, jabatan..."></x-data-search>
                <a class="btn btn-sm btn-outline-success d-flex align-items-center gap-1" href="{{ route('users.create') }}">
                    <i class="bi bi-plus-lg"></i> Tambah Pengguna
                </a>
            </div>
        </div>
    </div>

    {{-- Alerts --}}
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

    {{-- Filter Role --}}
    <div class="mb-3">
        <div class="btn-group" role="group">
            <a href="{{ route('users.index') }}" 
                class="btn btn-sm {{ !request('role') ? 'btn-primary' : 'btn-outline-primary' }}">
                Semua
            </a>
            <a href="{{ route('users.index', ['role' => 'admin']) }}" 
                class="btn btn-sm {{ request('role') === 'admin' ? 'btn-primary' : 'btn-outline-primary' }}">
                Admin
            </a>
            <a href="{{ route('users.index', ['role' => 'user']) }}" 
                class="btn btn-sm {{ request('role') === 'user' ? 'btn-primary' : 'btn-outline-primary' }}">
                User
            </a>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px" class="text-center">No</th>
                    <th>Nama</th>
                    <th>NRP</th>
                    <th>Jabatan</th>
                    <th class="text-center">Role</th>
                    <th style="width: 200px" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td class="text-center">{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $user->foto ? asset('storage/' . $user->foto) : asset('images/default-avatar.png') }}"
                                    alt="{{ $user->nama }}" class="rounded-circle object-fit-cover"
                                    style="width: 32px; height: 32px;">
                                <div>
                                    <div class="fw-semibold">{{ $user->nama }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->nrp }}</td>
                        <td>{{ $user->jabatan ?? '-' }}</td>
                        <td class="text-center">
                            @if ($user->role === 'admin')
                                <span class="badge bg-success">
                                    <i class="bi bi-shield-check me-1"></i>Admin
                                </span>
                            @else
                                <span class="badge bg-info">
                                    <i class="bi bi-person me-1"></i>User
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning text-white"
                                    title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <button type="button" class="btn btn-danger" title="Hapus"
                                    onclick="confirmDelete('{{ $user->id }}', '{{ $user->nama }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-5 me-2"></i>Tidak ada data pengguna
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>

    {{-- Hidden form for delete --}}
    <form id="deleteForm" action="" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function confirmDelete(userId, userName) {
            Swal.fire({
                title: 'Hapus Pengguna?',
                text: `Apakah Anda yakin ingin menghapus pengguna "${userName}"? Tindakan ini tidak dapat dibatalkan.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('deleteForm');
                    form.action = `{{ route('users.destroy', ':id') }}`.replace(':id', userId);
                    form.submit();
                }
            });
        }
    </script>
@endsection
