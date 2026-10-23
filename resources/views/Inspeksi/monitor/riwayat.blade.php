@extends('layouts.app')

@section('judul', 'Riwayat Inspeksi Monitor/TV')
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
            <form action="{{ route('inspeksi.monitor.riwayat') }}" method="GET" class="d-flex flex-wrap gap-2 align-items-center">
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
                    placeholder="Cari data Monitor" value="{{ request('search') }}">
                <button type="submit" class="btn btn-sm btn-outline-primary">Tampilkan</button>
                @if (request('search'))
                    <a href="{{ route('inspeksi.monitor.riwayat', ['month' => $month, 'year' => $year]) }}"
                        class="btn btn-sm btn-outline-secondary">Reset</a>
                @endif
            </form>

            <a href="{{ route('inspeksi.monitor.index') }}" class="btn btn-sm btn-outline-secondary">
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
                    @forelse ($inspections as $monitor)
                        <tr>
                            <td class="text-center">{{ $inspections->firstItem() + $loop->index }}</td>
                            <td>{{ $monitor->nomor_aset ?: '-' }}</td>
                            <td>{{ $monitor->merek ?: '-' }}</td>
                            <td>{{ $monitor->type ?: '-' }}</td>
                            <td>{{ $monitor->sn ?: '-' }}</td>
                            <td>{{ $monitor->lokasi ?: '-' }}</td>
                            <td>{{ $monitor->tanggal_inspeksi ? \Carbon\Carbon::parse($monitor->tanggal_inspeksi)->format('d M Y') : '-' }}</td>
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
                                    <a href="{{ route('inspeksi.monitor.edit', $monitor) }}" class="btn btn-warning text-white" title="Edit"><i class="bi bi-pencil"></i></a>
                                    @if ($isGroupLeader && !$monitor->approved_at)
                                        <form action="{{ route('inspeksi.monitor.approve', $monitor) }}" method="POST">@csrf<button class="btn btn-success" onclick="return confirm('Approve inspeksi ini?')" title="Approve"><i class="bi bi-check-lg"></i></button></form>
                                    @endif
                                    <form action="{{ route('inspeksi.monitor.destroy', $monitor) }}" method="POST">@csrf @method('DELETE')<button class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus data?')" title="Hapus"><i class="bi bi-trash"></i></button></form>
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
