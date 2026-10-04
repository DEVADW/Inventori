@extends('layouts.app')
@section('judul', 'Pemeliharaan Perangkat ICC')
@section('subjudul', 'Formulir Pemeliharaan Perangkat ICC')
@section('konten')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3">
        <x-counter-badge title="Total Pemeliharaan ICC" bgColor="bg-info-subtle" :counter="$iccs->total()" />
        <div class="d-flex flex-wrap gap-2">
            <x-data-search :action="route('inspeksi.icc.index')" placeholder="Cari data ICC" />
            <a href="{{ route('inspeksi.icc.export', request()->only('search')) }}" class="btn btn-sm btn-outline-success"><i
                    class="bi bi-file-excel"></i> Export Excel</a>
            @if ($isGroupLeader)
                <form action="{{ route('inspeksi.icc.approve-all') }}" method="POST">@csrf<input type="hidden"
                        name="search" value="{{ request('search') }}"><button class="btn btn-sm btn-success"
                        onclick="return confirm('Approve semua data yang belum disetujui?')"><i
                            class="bi bi-check2-all"></i> Approve Semua</button></form>
            @endif
            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                data-bs-target="#downloadApprovedModal"><i class="bi bi-file-zip"></i> Unduh PDF Approved</button>
            @if (Auth()->user()->nrp == 250504)
                {{-- Kerja mass --}}
                <button type="button" class="btn btn-danger btn-modern me-1" data-bs-toggle="modal" data-bs-target="#cloneModal">
                    <i class="fas fa-copy me-1"></i> Clone Inspeksi 💀  
                </button>
            @endif
            <a href="{{ route('inspeksi.icc.create') }}" class="btn btn-sm btn-outline-success"><i
                    class="bi bi-plus-lg"></i> Tambah Pemeliharaan</a>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">No</th>
                        <th>No. Lambung Unit</th>
                        <th>Tanggal Inspeksi</th>
                        <th>Lokasi</th>
                        <th>Inspektor</th>
                        <th>Status</th>
                        <th>Disetujui Oleh</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($iccs as $icc)
                        <tr>
                            <td class="text-center">{{ $iccs->firstItem() + $loop->index }}</td>
                            <td>{{ $icc->no_lambung_unit }}</td>
                            <td>{{ $icc->tanggal_inspeksi?->format('d M Y') }}</td>
                            <td>{{ $icc->lokasi_inspeksi }}</td>
                            <td>{{ $icc->IdKaryawan->nama ?: '-' }}</td>
                            <td><span
                                    class="badge {{ $icc->approved_at ? 'text-bg-success' : 'text-bg-warning' }}">{{ $icc->approved_at ? 'Approved' : 'Menunggu GL' }}</span>
                            </td>
                            <td>{{ $icc->approved_by ?: '-' }}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm gap-1">
                                    @if ($icc->photo_path)
                                        <button type="button" class="btn btn-info text-white" data-bs-toggle="modal"
                                            data-bs-target="#photoPreviewModal" data-photo-url="{{ asset('storage/' . $icc->photo_path) }}"
                                            data-photo-name="{{ $icc->no_lambung_unit ?: 'ICC' }}" title="Lihat Foto">
                                            <i class="bi bi-image"></i>
                                        </button>
                                    @endif
                                    <a href="{{ route('inspeksi.icc.pdf', $icc) }}" class="btn btn-danger" target="_blank"
                                        title="PDF"><i class="bi bi-file-pdf"></i></a>
                                    @if (!$icc->approved_at)
                                        <a href="{{ route('inspeksi.icc.edit', $icc) }}" class="btn btn-warning text-white"
                                            title="Edit"><i class="bi bi-pencil"></i></a>
                                        @if ($isGroupLeader)
                                            <form action="{{ route('inspeksi.icc.approve', $icc) }}" method="POST">
                                                @csrf<button class="btn btn-success"
                                                    onclick="return confirm('Approve data ini?')" title="Approve"><i
                                                        class="bi bi-check-lg"></i></button></form>
                                        @endif
                                    @endif
                                    <form action="{{ route('inspeksi.icc.destroy', $icc) }}" method="POST">@csrf
                                        @method('DELETE')<button class="btn btn-danger"
                                            onclick="return confirm('Yakin ingin menghapus data?')" title="Hapus"><i
                                                class="bi bi-trash"></i></button></form>
                                </div>
                            </td>
                    </tr>@empty<tr>
                            <td colspan="8" class="text-center text-muted py-4">Belum ada data pemeliharaan ICC.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($iccs->hasPages())
        <div class="mt-3">{{ $iccs->links() }}</div>
    @endif
    <x-download-approved-modal route="{{ route('inspeksi.icc.download-approved') }}" :search="request('search')" />
    @if (Auth()->user()->nrp == 250504)
        <x-clone-modal route="{{ route('inspeksi.icc.clone') }}"></x-clone-modal>
    @endif
    <x-photo-modal />
@endsection
