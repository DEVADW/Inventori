@extends('layouts.app')

@section('judul', 'Inspeksi Perangkat Monitor SS6')
@section('subjudul', 'Daftar Inspeksi SS6 Periode Berjalan')

@section('konten')

    <div class="card-header">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <x-counter-badge title="Total Unit TAB SS6" bgColor="bg-info-subtle" :counter="$totalAset" />
            <x-counter-badge title="Sudah Diinspeksi" bgColor="bg-success-subtle" :counter="$totalSudah" />
            <x-counter-badge title="Belum Diinspeksi" bgColor="bg-warning-subtle" :counter="$totalBelum" />

            <div class="d-flex flex-wrap align-items-center gap-2">
                <x-data-search :action="route('inspeksi.ss6.index')" placeholder="Cari no lambung/aset"></x-data-search>
                <a href="{{ route('inspeksi.ss6.export', request()->only('search')) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-file-excel"></i> Export Excel</a>
                <a href="{{ route('inspeksi.ss6.riwayat') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-clock-history"></i> Riwayat Inspeksi</a>
                @if ($isGroupLeader)
                    <form action="{{ route('inspeksi.ss6.approve-all') }}" method="POST">@csrf<input type="hidden" name="search" value="{{ request('search') }}"><button class="btn btn-sm btn-success" onclick="return confirm('Approve semua inspeksi yang belum disetujui?')"><i class="bi bi-check2-all"></i> Approve Semua</button></form>
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

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th>No Lambung / Code Unit</th>
                        <th>No Asset</th>
                        <th>Serial Number</th>
                        <th>Tanggal Inspeksi</th>
                        <th>Status</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assets as $asset)
                        @php $inspection = $asset->currentInspection; @endphp
                        <tr>
                            <td class="text-center">{{ $assets->firstItem() + $loop->index }}</td>
                            <td>
                                @if ($inspection)
                                    {{ $asset->code_unit }}
                                @else
                                    <a href="{{ route('inspeksi.ss6.create', $asset) }}" class="fw-semibold">{{ $asset->code_unit }}</a>
                                @endif
                            </td>
                            <td>{{ $asset->no_aset_tab ?: '-' }}</td>
                            <td>{{ $asset->serial_number_tab ?: '-' }}</td>
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
                                <div class="btn-group gap-1">
                                    @if (!$inspection)
                                        <a href="{{ route('inspeksi.ss6.create', $asset) }}" class="btn btn-sm btn-outline-primary" title="Inspeksi Sekarang">
                                            <i class="bi bi-clipboard-check"></i> Inspeksi
                                        </a>
                                    @else
                                        @if ($inspection->photo_path)
                                            <button type="button" class="btn btn-info btn-sm text-white" data-bs-toggle="modal"
                                                data-bs-target="#photoPreviewModal" data-photo-url="{{ asset('storage/' . $inspection->photo_path) }}"
                                                data-photo-name="{{ $asset->code_unit ?: 'SS6' }}" title="Lihat Foto"><i class="bi bi-image"></i></button>
                                        @endif
                                        <a href="{{ route('inspeksi.ss6.pdf', $inspection) }}" target="_blank" class="btn btn-danger btn-sm"><i class="bi bi-file-pdf"></i> PDF</a>
                                        <a href="{{ route('inspeksi.ss6.edit', $inspection) }}" class="btn btn-warning btn-sm text-white"><i class="bi bi-pencil"></i></a>
                                        @if ($isGroupLeader && !$inspection->approved_at)
                                            <form action="{{ route('inspeksi.ss6.approve', $inspection) }}" method="POST">@csrf<button class="btn btn-success btn-sm" onclick="return confirm('Approve inspeksi ini?')"><i class="bi bi-check-lg"></i></button></form>
                                        @endif
                                        <form action="{{ route('inspeksi.ss6.destroy', $inspection) }}" method="POST">@csrf @method('DELETE')<button class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data?')"><i class="bi bi-trash"></i></button></form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                Belum ada unit yang terdaftar sebagai TAB SS6. Tandai checkbox TAB SS6 di menu Unit Operasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($assets->hasPages())
        <div class="mt-3">{{ $assets->links() }}</div>
    @endif
    @if (Auth()->user()->nrp == 250504)
        <x-clone-modal route="{{ route('inspeksi.ss6.clone') }}"></x-clone-modal>
    @endif
    <x-photo-modal />

@endsection
