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
                    <th>Code Number Unit</th>
                    <th>Type Unit</th>
                    <th class="text-center">Kelengkapan</th>
                    <th>No SIM Card</th>
                    <th>Remark</th>
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
                        <td>{{ $items->simCard->nomor_msisdn ?? '-' }}</td>
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
@endsection
