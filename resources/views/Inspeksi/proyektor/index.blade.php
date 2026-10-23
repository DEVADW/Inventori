@extends('layouts.app')
@section('judul', 'Inspeksi Proyektor')
@section('subjudul', 'Daftar Inspeksi Proyektor Periode Berjalan')
@section('konten')
    <div class="card-header">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <x-counter-badge title="Total Aset" bgColor="bg-info-subtle" :counter="$totalAset" />
            <x-counter-badge title="Sudah Diinspeksi" bgColor="bg-success-subtle" :counter="$totalSudah" />
            <x-counter-badge title="Belum Diinspeksi" bgColor="bg-warning-subtle" :counter="$totalBelum" />
            <div class="d-flex flex-wrap gap-2">
                <x-data-search :action="route('inspeksi.proyektor.index')" placeholder="Cari data Proyektor" />
                <a href="{{ route('inspeksi.proyektor.export', request()->only('search')) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-file-excel"></i> Export Excel</a>
                <a href="{{ route('inspeksi.proyektor.riwayat') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-clock-history"></i> Riwayat Inspeksi</a>
                @if ($isGroupLeader)
                    <form action="{{ route('inspeksi.proyektor.approve-all') }}" method="POST">@csrf<input type="hidden" name="search" value="{{ request('search') }}"><button class="btn btn-sm btn-success" onclick="return confirm('Approve semua inspeksi yang belum disetujui?')"><i class="bi bi-check2-all"></i> Approve Semua</button></form>
                @endif
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                    data-bs-target="#downloadApprovedModal"><i class="bi bi-file-zip"></i> Unduh PDF Approved</button>
                @if (Auth()->user()->nrp == 250504)
                    <button type="button" class="btn btn-danger btn-modern me-1" data-bs-toggle="modal" data-bs-target="#cloneModal">
                        <i class="fas fa-copy me-1"></i> Clone Inspeksi 💀
                    </button>
                @endif
            </div>
        </div>
        <div class="mt-2 d-flex align-items-center gap-2">
            <span class="badge bg-primary-subtle text-primary-emphasis">Periode Inspeksi: {{ $periodLabel }}</span>
            @can('isAdmin')
                <a href="{{ route('settings.inspeksi.index') }}" class="small">Atur periode inspeksi</a>
            @endcan
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
                        <th>Lokasi</th>
                        <th>Tanggal Inspeksi</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assets as $asset)
                        @php $inspection = $asset->currentInspection; @endphp
                        <tr>
                            <td class="text-center">{{ $assets->firstItem() + $loop->index }}</td>
                            <td>
                                @if ($inspection)
                                    {{ $asset->no_aset }}
                                @else
                                    <a href="{{ route('inspeksi.proyektor.create', $asset) }}" class="fw-semibold">{{ $asset->no_aset }}</a>
                                @endif
                            </td>
                            <td>{{ $asset->merek }}</td>
                            <td>{{ $asset->tipe }}</td>
                            <td>{{ $asset->serial_number }}</td>
                            <td>{{ $asset->lokasi }}</td>
                            <td>{{ $inspection && $inspection->tanggal_inspeksi ? \Carbon\Carbon::parse($inspection->tanggal_inspeksi)->format('d M Y') : '-' }}</td>
                            <td>
                                @if (!$inspection)
                                    <span class="badge text-bg-secondary">Belum Diinspeksi</span>
                                @elseif ($inspection->approved_at)
                                    <span class="badge text-bg-success">Approved</span>
                                @else
                                    <span class="badge text-bg-warning">Menunggu GL</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm gap-1">
                                    @if (!$inspection)
                                        <a href="{{ route('inspeksi.proyektor.create', $asset) }}" class="btn btn-sm btn-outline-primary" title="Inspeksi Sekarang">
                                            <i class="bi bi-clipboard-check"></i> Inspeksi
                                        </a>
                                    @else
                                        @if ($inspection->photo_path)
                                            <button type="button" class="btn btn-info text-white" data-bs-toggle="modal"
                                                data-bs-target="#photoPreviewModal" data-photo-url="{{ asset('storage/' . $inspection->photo_path) }}"
                                                data-photo-name="{{ $asset->no_aset }}" title="Lihat Foto">
                                                <i class="bi bi-image"></i>
                                            </button>
                                        @endif
                                        <a href="{{ route('inspeksi.proyektor.pdf', $inspection) }}" class="btn btn-danger" target="_blank" title="PDF"><i class="bi bi-file-pdf"></i></a>
                                        <a href="{{ route('inspeksi.proyektor.edit', $inspection) }}" class="btn btn-warning text-white" title="Edit"><i class="bi bi-pencil"></i></a>
                                        @if ($isGroupLeader && !$inspection->approved_at)
                                            <form action="{{ route('inspeksi.proyektor.approve', $inspection) }}" method="POST">@csrf<button class="btn btn-success" onclick="return confirm('Approve inspeksi ini?')" title="Approve"><i class="bi bi-check-lg"></i></button></form>
                                        @endif
                                        <form action="{{ route('inspeksi.proyektor.destroy', $inspection) }}" method="POST">@csrf @method('DELETE')<button class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus data?')" title="Hapus"><i class="bi bi-trash"></i></button></form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-3">
                                Belum ada data aset Proyektor. Tambahkan dulu di menu Inventori Proyektor.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($assets->hasPages())
        <div class="card-footer bg-white">{{ $assets->links() }}</div>
    @endif
    <x-download-approved-modal route="{{ route('inspeksi.proyektor.download-approved') }}" :search="request('search')" />
    @if (Auth()->user()->nrp == 250504)
        <x-clone-modal route="{{ route('inspeksi.proyektor.clone') }}"></x-clone-modal>
    @endif
    <x-photo-modal />
@endsection
