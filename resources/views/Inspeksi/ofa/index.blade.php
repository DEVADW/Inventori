@extends('layouts.app')
@section('judul', 'Inspeksi Perangkat OFA')
@section('subjudul', 'Checklist Inspeksi Perangkat Onboard FleetSafe Assist')
@section('konten')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3">
        <x-counter-badge title="Total Inspeksi OFA" bgColor="bg-info-subtle" :counter="$ofas->total()" />
        <div class="d-flex flex-wrap gap-2"><x-data-search :action="route('inspeksi.ofa.index')" placeholder="Cari data OFA" />
            <a href="{{ route('inspeksi.ofa.export', request()->only('search')) }}" class="btn btn-sm btn-outline-success"><i
                    class="bi bi-file-excel"></i> Export Excel</a>
            @if ($isGroupLeader)
                <form action="{{ route('inspeksi.ofa.approve-all') }}" method="POST">@csrf<input type="hidden"
                        name="search" value="{{ request('search') }}"><button class="btn btn-sm btn-success"
                        onclick="return confirm('Approve semua inspeksi yang belum disetujui?')"><i
                            class="bi bi-check2-all"></i> Approve Semua</button></form>
            @endif
            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                data-bs-target="#downloadApprovedModal"><i class="bi bi-file-zip"></i> Unduh PDF Approved</button>
            @if (Auth()->user()->nrp == 250504)
                <button type="button" class="btn btn-danger btn-modern me-1" data-bs-toggle="modal"
                    data-bs-target="#cloneModal">
                    <i class="fas fa-copy me-1"></i> Clone Inspeksi 💀
                </button>
            @endif
            <a href="{{ route('inspeksi.ofa.create') }}" class="btn btn-sm btn-outline-success"><i
                    class="bi bi-plus-lg"></i> Tambah Inspeksi</a>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">No</th>
                        <th>Project</th>
                        <th>Code Number Unit</th>
                        <th>Type Unit</th>
                        <th>Serial Number Modul</th>
                        <th>Tanggal</th>
                        <th>Tim Pelaksana</th>
                        <th>Status</th>
                        <th>Disetujui Oleh</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ofas as $ofa)
                        <tr>
                            <td class="text-center">{{ $ofas->firstItem() + $loop->index }}</td>
                            <td>{{ $ofa->project_name ?: '-' }}</td>
                            <td>{{ $ofa->code_number_unit ?: '-' }}</td>
                            <td>{{ $ofa->type_unit ?: '-' }}</td>
                            <td>{{ $ofa->serial_number_modul ?: '-' }}</td>
                            <td>{{ $ofa->created_at?->format('d M Y') ?: '-' }}</td>
                            <td>{{ collect($ofa->tim_pelaksana ?? [])->pluck('nama')->join(', ') }}</td>
                            <td><span
                                    class="badge {{ $ofa->approved_at ? 'text-bg-success' : 'text-bg-warning' }}">{{ $ofa->approved_at ? 'Approved' : 'Menunggu GL' }}</span>
                            </td>
                            <td>{{ $ofa->diperiksa_oleh ?: '-' }}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm gap-1">
                                    @if ($ofa->photo_path)
                                        <button type="button" class="btn btn-info text-white" data-bs-toggle="modal"
                                            data-bs-target="#photoPreviewModal" data-photo-url="{{ asset('storage/' . $ofa->photo_path) }}"
                                            data-photo-name="{{ $ofa->code_number_unit ?: 'OFA' }}" title="Lihat Foto">
                                            <i class="bi bi-image"></i>
                                        </button>
                                    @endif

                                    <a href="{{ route('inspeksi.ofa.pdf', $ofa) }}" class="btn btn-danger" target="_blank"
                                        title="PDF"><i class="bi bi-file-pdf"></i></a>
                                    <a href="{{ route('inspeksi.ofa.edit', $ofa) }}"
                                        class="btn btn-warning text-white"title="Edit"><i class="bi bi-pencil"></i></a>
                                    @if (!$ofa->approved_at)
                                        @if ($isGroupLeader)
                                            <form action="{{ route('inspeksi.ofa.approve', $ofa) }}" method="POST">
                                                @csrf<button class="btn btn-success"
                                                    onclick="return confirm('Approve inspeksi ini?')" title="Approve"><i
                                                        class="bi bi-check-lg"></i></button></form>
                                        @endif
                                    @endif
                                    <form action="{{ route('inspeksi.ofa.destroy', $ofa) }}" method="POST">@csrf
                                        @method('DELETE')<button class="btn btn-danger"
                                            onclick="return confirm('Yakin ingin menghapus data?')" title="Hapus"><i
                                                class="bi bi-trash"></i></button></form>
                                </div>
                            </td>
                    </tr>@empty<tr>
                            <td colspan="10" class="text-center text-muted py-4">Belum ada data inspeksi OFA.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($ofas->hasPages())
        <div class="mt-3">{{ $ofas->links() }}</div>
    @endif
    @if (Auth()->user()->nrp == 250504)
        <x-clone-modal route="{{ route('inspeksi.ofa.clone') }}"></x-clone-modal>
    @endif
    <x-download-approved-modal route="{{ route('inspeksi.ofa.download-approved') }}" :search="request('search')" />
    <x-photo-modal />
@endsection
