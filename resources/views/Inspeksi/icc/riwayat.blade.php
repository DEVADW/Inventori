@extends('layouts.app')

@section('judul', 'Riwayat Pemeliharaan ICC')
@section('subjudul', 'Riwayat Hasil Pemeliharaan Per Bulan')

@php
    $bulanList = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];
@endphp

@section('konten')
    <div class="card-header">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <form action="{{ route('inspeksi.icc.riwayat') }}" method="GET" class="d-flex flex-wrap gap-2 align-items-center">
                <select name="month" class="form-select form-select-sm" style="width: auto">
                    @foreach ($bulanList as $num => $label)
                        <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="year" class="form-select form-select-sm" style="width: auto">
                    @foreach ($availableYears as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                <input type="search" name="search" class="form-control form-control-sm" style="width: 14rem"
                    placeholder="Cari data ICC" value="{{ request('search') }}">
                <button type="submit" class="btn btn-sm btn-outline-primary">Tampilkan</button>
                @if (request('search'))
                    <a href="{{ route('inspeksi.icc.riwayat', ['month' => $month, 'year' => $year]) }}"
                        class="btn btn-sm btn-outline-secondary">Reset</a>
                @endif
            </form>

            <a href="{{ route('inspeksi.icc.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Periode Berjalan
            </a>
        </div>
        <div class="mt-2">
            <span class="badge bg-secondary-subtle text-secondary-emphasis">
                Menampilkan riwayat: {{ $bulanList[$month] ?? $month }} {{ $year }}
            </span>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th>No. Lambung Unit</th>
                        <th>Tanggal Inspeksi</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th>Disetujui Oleh</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inspections as $icc)
                        <tr>
                            <td class="text-center">{{ $inspections->firstItem() + $loop->index }}</td>
                            <td>{{ $icc->no_lambung_unit }}</td>
                            <td>{{ $icc->tanggal_inspeksi?->format('d M Y') }}</td>
                            <td>{{ $icc->lokasi_inspeksi }}</td>
                            <td><span class="badge {{ $icc->approved_at ? 'text-bg-success' : 'text-bg-warning' }}">{{ $icc->approved_at ? 'Approved' : 'Menunggu GL' }}</span></td>
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
                                    <a href="{{ route('inspeksi.icc.pdf', $icc) }}" class="btn btn-danger" target="_blank" title="PDF"><i class="bi bi-file-pdf"></i></a>
                                    <a href="{{ route('inspeksi.icc.edit', $icc) }}" class="btn btn-warning text-white" title="Edit"><i class="bi bi-pencil"></i></a>
                                    @if ($isGroupLeader && !$icc->approved_at)
                                        <form action="{{ route('inspeksi.icc.approve', $icc) }}" method="POST">@csrf<button class="btn btn-success" onclick="return confirm('Approve data ini?')" title="Approve"><i class="bi bi-check-lg"></i></button></form>
                                    @endif
                                    <form action="{{ route('inspeksi.icc.destroy', $icc) }}" method="POST">@csrf @method('DELETE')<button class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus data?')" title="Hapus"><i class="bi bi-trash"></i></button></form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">
                                Belum ada riwayat pemeliharaan pada {{ $bulanList[$month] ?? $month }} {{ $year }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($inspections->hasPages())
        <div class="card-footer bg-white">{{ $inspections->links() }}</div>
    @endif
    <x-photo-modal />
@endsection
