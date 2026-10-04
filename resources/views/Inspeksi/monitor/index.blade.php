@extends('layouts.app')
@section('judul', 'Inspeksi Monitor/TV')
@section('subjudul', 'Daftar Data Inspeksi Monitor/TV')
@section('konten')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3">
        <x-counter-badge title="Total Data Inspeksi Monitor/TV" bgColor="bg-info-subtle" :counter="$monitors->total()" />
        <div class="d-flex flex-wrap gap-2">
            <x-data-search :action="route('inspeksi.monitor.index')" placeholder="Cari data Monitor" />
            <a href="{{ route('inspeksi.monitor.export', request()->only('search')) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-file-excel"></i> Export Excel</a>
            @if ($isGroupLeader)
                <form action="{{ route('inspeksi.monitor.approve-all') }}" method="POST">@csrf<input type="hidden" name="search" value="{{ request('search') }}"><button class="btn btn-sm btn-success" onclick="return confirm('Approve semua inspeksi yang belum disetujui?')"><i class="bi bi-check2-all"></i> Approve Semua</button></form>
            @endif
            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                data-bs-target="#downloadApprovedModal"><i class="bi bi-file-zip"></i> Unduh PDF Approved</button>
            @if (Auth()->user()->nrp == 250504)
                <button type="button" class="btn btn-danger btn-modern me-1" data-bs-toggle="modal" data-bs-target="#cloneModal">
                    <i class="fas fa-copy me-1"></i> Clone Inspeksi 💀
                </button>
            @endif
            <a href="{{ route('inspeksi.monitor.create') }}" class="btn btn-sm btn-outline-success"><i class="bi bi-plus-lg"></i> Tambah Inspeksi</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th>Nomor Aset</th>
                    <th>Merek</th>
                    <th>Tipe</th>
                    <th>SN</th>
                    <th>Departemen</th>
                    <th>Lokasi</th>
                    <th>Tanggal Inspeksi</th>
                    <th>Status</th>
                    <th>Disetujui Oleh</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($monitors as $monitor)
                    <tr>
                        <td class="text-center">{{ $monitors->firstItem() + $loop->index }}</td>
                        <td>{{ $monitor->nomor_aset ?: '-' }}</td>
                        <td>{{ $monitor->merek ?: '-' }}</td>
                        <td>{{ $monitor->type ?: '-' }}</td>
                        <td>{{ $monitor->sn ?: '-' }}</td>
                        <td>{{ $monitor->departemen ?: '-' }}</td>
                        <td>{{ $monitor->lokasi ?: '-' }}</td>
                        <td>{{ $monitor->tanggal_inspeksi ? \Carbon\Carbon::parse($monitor->tanggal_inspeksi)->format('d M Y') : '-' }}
                        </td>
                        <td><span class="badge {{ $monitor->approved_at ? 'text-bg-success' : 'text-bg-warning' }}">{{ $monitor->approved_at ? 'Approved' : 'Menunggu GL' }}</span></td>
                        <td>{{ $monitor->approved_by ?: '-' }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm gap-1">
                                @if ($monitor->photo_path)
                                    <button type="button" class="btn btn-info text-white" data-bs-toggle="modal"
                                        data-bs-target="#photoPreviewModal" data-photo-url="{{ asset('storage/' . $monitor->photo_path) }}"
                                        data-photo-name="{{ $monitor->nomor_aset ?: 'Monitor/TV' }}" title="Lihat Foto">
                                        <i class="bi bi-image"></i>
                                    </button>
                                @endif
                                <a href="{{ route('inspeksi.monitor.pdf', $monitor) }}" class="btn btn-danger" target="_blank" title="PDF"><i class="bi bi-file-pdf"></i></a>
                                @if ($monitor->approved_at)
                                @else
                                    <a href="{{ route('inspeksi.monitor.edit', $monitor) }}" class="btn btn-warning text-white" title="Edit"><i class="bi bi-pencil"></i></a>
                                    @if ($isGroupLeader)
                                    <form action="{{ route('inspeksi.monitor.approve', $monitor) }}" method="POST">@csrf<button class="btn btn-success" onclick="return confirm('Approve inspeksi ini?')" title="Approve"><i class="bi bi-check-lg"></i></button></form>
                                    @endif
                                @endif
                                <form action="{{ route('inspeksi.monitor.destroy', $monitor) }}" method="POST">@csrf @method('DELETE')<button class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus data?')" title="Hapus"><i class="bi bi-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                @empty <tr>
                        <td colspan="10" class="text-center text-muted py-3">Belum ada data inspeksi Monitor/TV.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($monitors->hasPages())
    <div class="mt-3">{{ $monitors->links() }}</div>@endif
    <x-download-approved-modal route="{{ route('inspeksi.monitor.download-approved') }}" :search="request('search')" />
    @if (Auth()->user()->nrp == 250504)
        <x-clone-modal route="{{ route('inspeksi.monitor.clone') }}"></x-clone-modal>
    @endif
    <x-photo-modal />
@endsection
