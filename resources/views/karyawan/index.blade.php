@extends('layouts.app')
@section('judul', 'Daftar Karyawan')
@section('subjudul', 'Index Daftar Karyawan')

@section('konten')
    @php
        // mapping jabatan supaya bagus gak data dari database langsung
        $jabatan = [
            'SH' => 'Section Head',
            'GL' => 'Group Leader',
            'ICT' => 'ICT',
            'hardware_enggineer' => 'Hardware Engineer',
            'ICT_technician' => 'ICT Technician',
            'helper' => 'Helper ICT',
        ];
    @endphp

    <div class="card-header">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <div class="card bg-info-subtle mb-0" style="width: 12rem;">
                <div class="card-body p-2 text-center">
                    <h6 class="card-title mb-1 small fw-bold text-uppercase">Total data karyawan ICT</h6>
                    <h4 class="card-text fw-bold mb-0">{{ $dataKaryawan->count() }}</h4>
                </div>
            </div>

            <div class="d-flex flex-column flex-sm-row align-items-end align-items-sm-center gap-2">
                <x-data-search :action="route('karyawan.index')" placeholder="Cari data karyawan"></x-data-search>
                <a class="btn btn-sm btn-outline-success d-flex align-items-center gap-1"
                    href="{{ route('karyawan.create') }}">
                    <i class="bi bi-plus-lg"></i> Tambah Karyawan
                </a>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 mt-3 mb-3">
        {{-- <a id="export-csv" href="{{ route('karyawan.export') }}" class="btn btn-sm btn-outline-success">
            <i class="bi bi-file-excel me-1" aria-hidden="true" style="hidden"></i>
            Export Excel
        </a> --}}


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
                    <th>NRP/NIK</th>
                    <th>Jabatan</th>
                    <th>Departemen</th>
                    <th>QR CODE</th>
                    <th style="width: 150px" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dataKaryawan as $k)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $k->nama }}</td>
                        <td>{{ $k->nrp }}</td>
                        <td>
                            {{ $jabatan[$k->jabatan] ?? $k->jabatan }}

                        </td>
                        <td>{{ $k->departemen }}</td>
                        <td class="align-items-center">{!! QrCode::size(100)->generate($k->qr_code) !!}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm gap-1" role="group">
                                {{-- <a href="{{ route('', $tamu->id) }}" class="btn btn-info text-white"
                        title="Detail">
                        <i class="bi bi-eye"></i>
                        </a> --}}
                                <a href="{{ route('karyawan.edit', $k->id) }}" class="btn btn-sm btn-warning text-white"
                                    title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('karyawan.destroy', $k->id) }}" id="form-delete-{{ $k->id }}"
                                    method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="deleteConfirm('form-delete-{{ $k->id }}')"
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
                            Belum ada data karaywan.
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

    {{ $dataKaryawan->links() }}


@endsection
