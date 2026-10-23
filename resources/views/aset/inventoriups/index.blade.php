@extends('layouts.app')
@section('judul', 'Inventori UPS')
@section('subjudul', 'Index Inventori UPS')

@section('konten')
    <div class="card-header">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <x-counter-badge title='Total Data UPS' bgColor="bg-info-subtle" :counter="$dataInventoriUps->count()"></x-counter-badge>

            <div class="d-flex flex-column flex-sm-row align-items-end align-items-sm-center gap-2">
                <x-data-search :action="route('aset.inventoriups.index')" placeholder="Cari data UPS"></x-data-search>
                <a class="btn btn-sm btn-outline-success d-flex align-items-center gap-1"
                    href="{{ route('aset.inventoriups.create') }}">
                    <i class="bi bi-plus-lg"></i> Tambah UPS
                </a>
            </div>
        </div>
    </div>

    <div class="table-responsive mt-3">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th style="width: 50px" class="text-center">No</th>
                    <th>Nomor Aset</th>
                    <th>Merek</th>
                    <th>Tipe</th>
                    <th>Serial Number</th>
                    <th>Department</th>
                    <th>Lokasi</th>
                    <th style="width: 150px" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dataInventoriUps as $items)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $items->no_aset }}</td>
                        <td>{{ $items->merek }}</td>
                        <td>{{ $items->tipe }}</td>
                        <td>{{ $items->serial_number }}</td>
                        <td>{{ $items->department }}</td>
                        <td>{{ $items->lokasi }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm gap-1" role="group">
                                <a href="{{ route('aset.inventoriups.edit', $items->id) }}"
                                    class="btn btn-sm btn-warning text-white" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('aset.inventoriups.destroy', $items->id) }}"
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
                        <td colspan="8" class="text-center text-muted py-3">
                            Belum ada data Inventori UPS
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer text-secondary small"></div>
    {{ $dataInventoriUps->links() }}
@endsection
