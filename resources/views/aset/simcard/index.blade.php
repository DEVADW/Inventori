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
                <button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"
                    data-bs-toggle="modal" data-bs-target="#importSimCardModal">
                    <i class="bi bi-file-earmark-arrow-up"></i> Import Excel
                </button>
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
                    <x-sortable-th field="nomor_msisdn" label="Nomor MSISDN" />
                    <x-sortable-th field="jumlah_paket_kuota" label="Jumlah Paket Kuota" />
                    <x-sortable-th field="status_kartu" label="Status Kartu" class="text-center" />
                    <th>Associate</th>
                    <x-sortable-th field="remark" label="Remark" />
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
                        <td>{{ $items->unitOperasi->code_unit ?? '-' }}</td>
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

    <div class="modal fade" id="importSimCardModal" tabindex="-1" aria-labelledby="importSimCardModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('aset.simcard.import') }}" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importSimCardModalLabel">Import Data SIM Card</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-3">
                        Unggah <strong>data_aset.xlsx</strong>. Kolom B: Nomor MSISDN, C: Jumlah Paket Kuota,
                        D: Status Kartu, E: Remark. Baris judul boleh disertakan.
                    </p>
                    <label for="sim-card-file" class="form-label">File Excel</label>
                    <input id="sim-card-file" type="file" name="file" class="form-control @error('file') is-invalid @enderror"
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
                    <div class="form-text">Status kartu: Terpasang, Standby, atau Nonaktif.</div>
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
                new bootstrap.Modal(document.getElementById('importSimCardModal')).show();
            });
        </script>
    @endif
@endpush
