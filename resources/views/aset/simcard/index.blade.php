@extends('layouts.app')
@section('judul', 'Data SIM Card')
@section('subjudul', 'Index Data SIM Card')

@section('konten')
    <div class="card-header">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <x-counter-badge title='Terpasang' bgColor="bg-success-subtle" :counter="$totalTerpasang"></x-counter-badge>
            <x-counter-badge title='Standby' bgColor="bg-info-subtle" :counter="$totalStandby"></x-counter-badge>
            <x-counter-badge title='Nonaktif' bgColor="bg-danger-subtle" :counter="$totalNonaktif"></x-counter-badge>

            <div class="d-flex flex-column flex-sm-row align-items-end align-items-sm-center gap-2">
                <x-data-search :action="route('aset.simcard.index')" placeholder="Cari nomor MSISDN"></x-data-search>
                <a class="btn btn-sm btn-outline-success d-flex align-items-center gap-1"
                    href="{{ route('aset.simcard.create') }}">
                    <i class="bi bi-plus-lg"></i> Tambah SIM Card
                </a>
            </div>
        </div>
    </div>

    <div class="table-responsive mt-3">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th style="width: 50px" class="text-center">No</th>
                    <th>Nomor MSISDN</th>
                    <th>Jumlah Paket Kuota</th>
                    <th class="text-center">Status Kartu</th>
                    <th>Associate</th>
                    <th>Remark</th>
                    <th style="width: 150px" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dataSimCard as $items)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $items->nomor_msisdn }}</td>
                        <td>{{ $items->jumlah_paket_kuota ?? '-' }}</td>
                        <td class="text-center">
                            @php
                                $badgeClass = match ($items->status_kartu) {
                                    'Terpasang' => 'bg-success',
                                    'Standby' => 'bg-info',
                                    'Nonaktif' => 'bg-danger',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $items->status_kartu }}</span>
                        </td>
                        <td>{{ $items->associate ?? '-' }}</td>
                        <td>{{ $items->remark ?? '-' }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm gap-1" role="group">
                                <a href="{{ route('aset.simcard.edit', $items->id) }}"
                                    class="btn btn-sm btn-warning text-white" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('aset.simcard.destroy', $items->id) }}"
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
                            Belum ada data SIM Card
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer text-secondary small"></div>
    {{ $dataSimCard->links() }}
@endsection
