@extends('layouts.app')
@section('judul', 'Inventori Stavolt')
@section('subjudul', 'Index Inventori Stavolt')

@section('konten')
    <div class="card-header">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <x-counter-badge title='Total Data Stavolt' bgColor="bg-info-subtle" :counter="$dataInventoriStavolt->count()"></x-counter-badge>

            <div class="d-flex flex-column flex-sm-row align-items-end align-items-sm-center gap-2">
                <x-data-search :action="route('aset.inventoristavolt.index')" placeholder="Cari data stavolt"></x-data-search>
                <button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"
                    data-bs-toggle="modal" data-bs-target="#importInventoriStavoltModal">
                    <i class="bi bi-file-earmark-arrow-up"></i> Import Excel
                </button>
                <a class="btn btn-sm btn-outline-success d-flex align-items-center gap-1"
                    href="{{ route('aset.inventoristavolt.create') }}">
                    <i class="bi bi-plus-lg"></i> Tambah Stavolt
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
                @forelse ($dataInventoriStavolt as $items)
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
                                <a href="{{ route('aset.inventoristavolt.edit', $items->id) }}"
                                    class="btn btn-sm btn-warning text-white" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('aset.inventoristavolt.destroy', $items->id) }}"
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
                            Belum ada data Inventori Stavolt
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer text-secondary small"></div>
    {{ $dataInventoriStavolt->links() }}

    <div class="modal fade" id="importInventoriStavoltModal" tabindex="-1" aria-labelledby="importInventoriStavoltModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('aset.inventoristavolt.import') }}" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importInventoriStavoltModalLabel">Import Data Inventori Stavolt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-3">
                        Unggah <strong>data_aset.xlsx</strong>. Baris 1 wajib digunakan sebagai header; data dimulai dari baris 2.
                        Kolom B: Nomor Aset, C: Merek, D: Tipe, E: Serial Number, F: Department, G: Lokasi.
                    </p>
                    <label for="inventori-stavolt-file" class="form-label">File Excel</label>
                    <input id="inventori-stavolt-file" type="file" name="file" class="form-control @error('file') is-invalid @enderror"
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
                new bootstrap.Modal(document.getElementById('importInventoriStavoltModal')).show();
            });
        </script>
    @endif
@endpush
