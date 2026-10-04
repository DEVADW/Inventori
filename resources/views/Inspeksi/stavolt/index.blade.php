@extends('layouts.app')

@section('judul', 'Inspeksi Stavolt')
@section('subjudul', 'Daftar Data Inspeksi Stavolt')

@section('konten')
    <div class="card-header">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <x-counter-badge title="Total Data Inspeksi Stavolt" bgColor="bg-info-subtle" :counter="$stavolts->total()" />
            <div class="d-flex flex-wrap gap-2">
                <x-data-search :action="route('inspeksi.stavolt.index')" placeholder="Cari data Stavolt" />
                <a href="{{ route('inspeksi.stavolt.export', request()->only('search')) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-file-excel"></i> Export Excel</a>
                @if ($isGroupLeader)
                    <form action="{{ route('inspeksi.stavolt.approve-all') }}" method="POST">@csrf<input type="hidden" name="search" value="{{ request('search') }}"><button class="btn btn-sm btn-success" onclick="return confirm('Approve semua inspeksi yang belum disetujui?')"><i class="bi bi-check2-all"></i> Approve Semua</button></form>
                @endif
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                    data-bs-target="#downloadApprovedModal"><i class="bi bi-file-zip"></i> Unduh PDF Approved</button>
                @if (Auth()->user()->nrp == 250504)
                    <button type="button" class="btn btn-danger btn-modern me-1" data-bs-toggle="modal" data-bs-target="#cloneModal">
                        <i class="fas fa-copy me-1"></i> Clone Inspeksi 💀
                    </button>
                @endif
                <a class="btn btn-sm btn-outline-success" href="{{ route('inspeksi.stavolt.create') }}">
                    <i class="bi bi-plus-lg"></i> Tambah Inspeksi
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
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
                    @forelse ($stavolts as $stavolt)
                        <tr>
                            <td class="text-center">{{ $stavolts->firstItem() + $loop->index }}</td>
                            <td>{{ $stavolt->nomor_aset ?: '-' }}</td>
                            <td>{{ $stavolt->merek ?: '-' }}</td>
                            <td>{{ $stavolt->type ?: '-' }}</td>
                            <td>{{ $stavolt->sn ?: '-' }}</td>
                            <td>{{ $stavolt->departemen ?: '-' }}</td>
                            <td>{{ $stavolt->lokasi ?: '-' }}</td>
                            <td>{{ $stavolt->tanggal_inspeksi ? \Carbon\Carbon::parse($stavolt->tanggal_inspeksi)->format('d M Y') : '-' }}
                            </td>
                            <td><span class="badge {{ $stavolt->approved_at ? 'text-bg-success' : 'text-bg-warning' }}">{{ $stavolt->approved_at ? 'Approved' : 'Menunggu GL' }}</span></td>
                            <td>{{ $stavolt->approved_by ?: '-' }}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm gap-1">
                                    @if ($stavolt->photo_path)
                                        <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal"
                                            data-bs-target="#photoPreviewModal" data-photo-url="{{ asset('storage/' . $stavolt->photo_path) }}"
                                            data-photo-name="{{ $stavolt->nomor_aset ?: 'Stavolt' }}" title="Lihat Foto">
                                            <i class="bi bi-image"></i>
                                        </button>
                                    @endif
                                    <a href="{{ route('inspeksi.stavolt.pdf', $stavolt) }}" class="btn btn-danger" target="_blank" title="PDF"><i class="bi bi-file-pdf"></i></a>
                                        <a href="{{ route('inspeksi.stavolt.edit', $stavolt) }}" class="btn btn-warning text-white" title="Edit"><i class="bi bi-pencil"></i></a>
                                        @if ($isGroupLeader)
                                        <form action="{{ route('inspeksi.stavolt.approve', $stavolt) }}" method="POST">@csrf<button class="btn btn-success" onclick="return confirm('Approve inspeksi ini?')" title="Approve"><i class="bi bi-check-lg"></i></button></form>
                                        @endif
                                    <form action="{{ route('inspeksi.stavolt.destroy', $stavolt) }}" method="POST">@csrf @method('DELETE')<button class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus data?')" title="Hapus"><i class="bi bi-trash"></i></button></form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-3">Belum ada data inspeksi Stavolt.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($stavolts->hasPages())
        <div class="card-footer bg-white">{{ $stavolts->links() }}</div>
    @endif
    <x-download-approved-modal route="{{ route('inspeksi.stavolt.download-approved') }}" :search="request('search')" />
    @if (Auth()->user()->nrp == 250504)
        <x-clone-modal route="{{ route('inspeksi.stavolt.clone') }}"></x-clone-modal>
    @endif
    <x-photo-modal />
    </div>
@endsection
