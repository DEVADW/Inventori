@extends('layouts.app')

@section('judul', 'Riwayat Inspeksi UPS')
@section('subjudul', 'Riwayat Hasil Inspeksi Per Bulan')

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
            <form action="{{ route('inspeksi.ups.riwayat') }}" method="GET" class="d-flex flex-wrap gap-2 align-items-center">
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
                    placeholder="Cari data UPS" value="{{ request('search') }}">
                <button type="submit" class="btn btn-sm btn-outline-primary">Tampilkan</button>
                @if (request('search'))
                    <a href="{{ route('inspeksi.ups.riwayat', ['month' => $month, 'year' => $year]) }}"
                        class="btn btn-sm btn-outline-secondary">Reset</a>
                @endif
            </form>

            <a href="{{ route('inspeksi.ups.index') }}" class="btn btn-sm btn-outline-secondary">
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
                        <th>Nomor Aset</th>
                        <th>Merek</th>
                        <th>Tipe</th>
                        <th>SN</th>
                        <th>Lokasi</th>
                        <th>Tanggal Inspeksi</th>
                        <th>Status</th>
                        <th>Disetujui Oleh</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inspections as $ups)
                        <tr>
                            <td class="text-center">{{ $inspections->firstItem() + $loop->index }}</td>
                            <td>{{ $ups->nomor_aset ?: '-' }}</td>
                            <td>{{ $ups->merek ?: '-' }}</td>
                            <td>{{ $ups->type ?: '-' }}</td>
                            <td>{{ $ups->sn ?: '-' }}</td>
                            <td>{{ $ups->lokasi ?: '-' }}</td>
                            <td>{{ $ups->tanggal_inspeksi ? \Carbon\Carbon::parse($ups->tanggal_inspeksi)->format('d M Y') : '-' }}</td>
                            <td><span class="badge {{ $ups->approved_at ? 'text-bg-success' : 'text-bg-warning' }}">{{ $ups->approved_at ? 'Approved' : 'Menunggu GL' }}</span></td>
                            <td>{{ $ups->approved_by ?: '-' }}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm gap-1">
                                    @if ($ups->photo_path)
                                        <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal"
                                            data-bs-target="#photoPreviewModal" data-photo-url="{{ asset('storage/' . $ups->photo_path) }}"
                                            data-photo-name="{{ $ups->nomor_aset ?: 'UPS' }}" title="Lihat Foto">
                                            <i class="bi bi-image"></i>
                                        </button>
                                    @endif
                                    <a href="{{ route('inspeksi.ups.pdf', $ups) }}" class="btn btn-danger" target="_blank" title="PDF"><i class="bi bi-file-pdf"></i></a>
                                    <a href="{{ route('inspeksi.ups.edit', $ups) }}" class="btn btn-warning text-white" title="Edit"><i class="bi bi-pencil"></i></a>
                                    @if ($isGroupLeader && !$ups->approved_at)
                                        <form action="{{ route('inspeksi.ups.approve', $ups) }}" method="POST">@csrf<button class="btn btn-success" onclick="return confirm('Approve inspeksi ini?')" title="Approve"><i class="bi bi-check-lg"></i></button></form>
                                    @endif
                                    <form action="{{ route('inspeksi.ups.destroy', $ups) }}" method="POST">@csrf @method('DELETE')<button class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus data?')" title="Hapus"><i class="bi bi-trash"></i></button></form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-3">
                                Belum ada riwayat inspeksi pada {{ $bulanList[$month] ?? $month }} {{ $year }}.
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
