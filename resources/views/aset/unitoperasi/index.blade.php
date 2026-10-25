@extends('layouts.app')
@section('judul', 'Data Unit Operasi')
@section('subjudul', 'Index Data Unit Operasi')

@section('konten')
    <div class="card-header">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <x-counter-badge title='Unit OFA' bgColor="bg-info-subtle" :counter="$totalOfa"></x-counter-badge>
            <x-counter-badge title='Unit ICC' bgColor="bg-info-subtle" :counter="$totalIcc"></x-counter-badge>
            <x-counter-badge title='Unit TAB SS6' bgColor="bg-info-subtle" :counter="$totalSs6"></x-counter-badge>

            <div class="d-flex flex-column flex-sm-row align-items-end align-items-sm-center gap-2">
                <x-data-search :action="route('aset.unitoperasi.index')" placeholder="Cari code/type unit"></x-data-search>
                <button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"
                    data-bs-toggle="modal" data-bs-target="#importUnitOperasiModal">
                    <i class="bi bi-file-earmark-arrow-up"></i> Import Excel
                </button>
                <a class="btn btn-sm btn-outline-success d-flex align-items-center gap-1"
                    href="{{ route('aset.unitoperasi.create') }}">
                    <i class="bi bi-plus-lg"></i> Tambah Unit
                </a>
            </div>
        </div>
    </div>

    <div class="table-responsive mt-3">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th style="width: 50px" class="text-center">No</th>
                    <x-sortable-th field="code_unit" label="Code Number Unit" />
                    <x-sortable-th field="type_unit" label="Type Unit" />
                    <th class="text-center">Kelengkapan</th>
                    <th>No SIM Card</th>
                    <x-sortable-th field="remark" label="Remark" />
                    <th style="width: 150px" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dataUnitOperasi as $items)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $items->code_unit }}</td>
                        <td>{{ $items->type_unit }}</td>
                        <td class="text-center">
                            @if($items->ofa)
                                <span class="badge bg-primary">OFA</span>
                            @endif
                            @if($items->icc)
                                <span class="badge bg-success">ICC</span>
                            @endif
                            @if($items->ss6)
                                <span class="badge bg-warning text-dark">TAB SS6</span>
                            @endif
                            @if(!$items->ofa && !$items->icc && !$items->ss6)
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $items->simCards->pluck('nomor_msisdn')->implode(', ') ?: '-' }}</td>
                        <td>{{ $items->remark ?? '-' }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm gap-1" role="group">
                                <a href="{{ route('aset.unitoperasi.edit', $items->id) }}"
                                    class="btn btn-sm btn-warning text-white" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('aset.unitoperasi.destroy', $items->id) }}"
                                    id="form-delete-{{ $items->id }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="deleteConfirm('form-delete-{{ $items->id }}')"
                                        class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">
                            Belum ada data Unit Operasi
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer text-secondary small"></div>
    {{ $dataUnitOperasi->links() }}

    <div class="modal fade" id="importUnitOperasiModal" tabindex="-1" aria-labelledby="importUnitOperasiModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('aset.unitoperasi.import') }}" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importUnitOperasiModalLabel">Import Data Unit Operasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-3">
                        Unggah <strong>data_aset.xlsx</strong>. Baris 1 wajib digunakan sebagai header; data dimulai dari baris 2.
                        Kolom B: Code Number Unit, C: Tipe Unit, D: No SIM Card, E: OFA, F: ICC, G: SS6,
                        H: Serial Number Module, I: Serial Number TAB, J: No Aset TAB, K: Remark.
                    </p>
                    <label for="unit-operasi-file" class="form-label">File Excel</label>
                    <input id="unit-operasi-file" type="file" name="file" class="form-control @if ($errors->has('file')) is-invalid @endif"
                        accept=".xlsx" required>
                    @if ($errors->has('file'))
                        <div class="invalid-feedback d-block">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->get('file') as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="form-text">
                        OFA, ICC, dan SS6 menerima Ya/Tidak, True/False, atau 1/0. Kosong berarti Tidak.
                        Kolom H digunakan bila OFA aktif; kolom I dan J digunakan bila SS6 aktif.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    @if ($errors->has('file'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                new bootstrap.Modal(document.getElementById('importUnitOperasiModal')).show();
            });
        </script>
    @endif
@endpush
