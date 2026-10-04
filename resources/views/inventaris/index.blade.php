    @extends('layouts.app')
    @section('judul', 'Inventaris')
    @section('subjudul', 'Index Inventaris')

    @section('konten')
        <div class="card-header">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                <x-counter-badge title='Total Data Inventaris' bgColor="bg-info-subtle" :counter="$dataInventaris->count()"></x-counter-badge>
                <x-counter-badge title='Total Barang Dipinjam' bgColor="bg-warning-subtle" :counter="$totalBelumDikembalikan"></x-counter-badge>
                <x-counter-badge title='Total Barang Dikembalikan' bgColor="bg-success-subtle" :counter="$totalDikembalikan"></x-counter-badge>
              

                <div class="d-flex flex-column flex-sm-row align-items-end align-items-sm-center gap-2">
                    <x-data-search :action="route('inventaris.index')" placeholder="Cari data barang"></x-data-search>
                    <a class="btn btn-sm btn-outline-success d-flex align-items-center gap-1"
                        href="{{ route('inventaris.create') }}">
                        <i class="bi bi-plus-lg"></i> Tambah Barang
                    </a>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 mt-3 mb-3">
            <a id="export-csv" href="{{ route('inventaris.export') }}"class="btn btn-sm btn-outline-success">
                <i class="bi bi-file-excel me-1" aria-hidden="true"></i>
                Export Excel
            </a>

            {{-- gak kepakek --}}
            {{-- <button id="print-table" type="button" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-printer me-1" aria-hidden="true"></i>
                Print
            </button> --}}
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px" class="text-center">No</th>
                        <th>Nama</th>
                        <th>NRP</th>
                        <th>Nama Asset</th>
                        <th>No Asset</th>
                        <th class="">Status Peminjaman</th>
                        <th>Tanggal Peminjaman</th>
                        <th>Tanggal Kembali</th>
                        <th style="width: 150px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dataInventaris as $items)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $items->nama }}</td>
                            <td>{{ $items->nrp }}</td>
                            <td>{{ $items->nama_perangkat }}</td>
                            <td>{{ $items->no_asset }}</td>
                            <td class="text-center">

                                <button type="button"
                                    class="btn btn-sm text-white {{ $items->status_peminjaman == 'Dikembalikan' ? 'bg-success' : 'bg-danger' }}"
                                    data-bs-toggle="modal" data-bs-target="#editStatusModal"
                                    data-bs-url="{{ route('inventaris.returnStatus', $items->id) }}"
                                    data-bs-nama="{{ $items->nama }}" data-bs-perangkat="{{ $items->nama_perangkat }}"
                                    data-bs-asset="{{ $items->no_asset }}" data-bs-nrp="{{ $items->nrp }}">
                                    {{ $items->status_peminjaman ?? 'Belum Dikembalikan' }}
                                </button>

                            </td>
                            <td>{{ \Carbon\Carbon::parse($items->tanggal_peminjaman)->format('d M Y') }}</td>
                            <td>{{ $items->status_peminjaman == 'Dikembalikan' ? \Carbon\Carbon::parse($items->tanggal_pengembalian)->format('d M Y') ?? '-' : '-' }}

                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm gap-1" role="group">
                                    {{-- gak kepakek --}}
                                    {{-- <a href="{{ route('', $inventaris->id) }}" class="btn btn-info text-white"
                                                title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a> --}}
                                    <a href="{{ route('inventaris.edit', $items->id) }}"
                                        class="btn btn-sm btn-warning text-white" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('inventaris.destroy', $items->id) }}"
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
                            <td colspan="9" class="text-center text-muted py-3">
                                Belum ada data Inventaris
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer text-secondary small">
            {{-- Powered by
            <a href="https://tabulator.info/" target="_blank" rel="noopener">Tabulator</a>
            &mdash; vanilla JS, no jQuery required. --}}
        </div>
        {{-- modal status --}}
        <x-modal-status-change></x-modal-status-change>
	{{$dataInventaris->links()}}
    @endsection
