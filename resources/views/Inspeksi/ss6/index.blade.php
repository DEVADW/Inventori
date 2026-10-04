@extends('layouts.app')

@section('judul', 'Inspeksi Perangkat Monitor SS6')
@section('subjudul', 'Data Inspeksi Monitor SS6')

@section('konten')

   <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3">

      <x-counter-badge title="Total Data Inspeksi Monitor" bgColor="bg-info-subtle" :counter="$dataSs6->total()" />
      <div class="d-flex flex-wrap align-items-center gap-2">
         <x-data-search :action="route('inspeksi.ss6.index')" placeholder="Cari data Inspeksi"></x-data-search>
         <a href="{{ route('inspeksi.ss6.export', request()->only('search')) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-file-excel"></i> Export Excel</a>
         @if ($isGroupLeader)
            <form action="{{ route('inspeksi.ss6.approve-all') }}" method="POST">@csrf<input type="hidden" name="search" value="{{ request('search') }}"><button class="btn btn-sm btn-success" onclick="return confirm('Approve semua inspeksi yang belum disetujui?')"><i class="bi bi-check2-all"></i> Approve Semua</button></form>
         @endif
          <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
             data-bs-target="#downloadApprovedModal"><i class="bi bi-file-zip"></i> Unduh PDF Approved</button>
         <x-download-approved-modal route="{{ route('inspeksi.ss6.download-approved') }}" :search="request('search')" />
         @if (Auth()->user()->nrp == 250504)
            <button type="button" class="btn btn-danger btn-modern me-1" data-bs-toggle="modal" data-bs-target="#cloneModal">
               <i class="fas fa-copy me-1"></i> Clone Inspeksi 💀
            </button>
         @endif
         <a class="btn btn-sm btn-outline-success d-flex align-items-center gap-1"
            href="{{ route('inspeksi.ss6.create') }}">
            <i class="bi bi-plus-lg"></i> Tambah Inspeksi
         </a>
      </div>

   </div>

   <div class="card shadow-sm">

      <div class="table-responsive">

         <table class="table table-hover table-bordered align-middle mb-0">

            <thead class="table-light">

               <tr>

                  <th width="5%" class="text-center">No</th>
                  <th>No Asset</th>
                  <th>No Lambung</th>
                  <th>Serial Number</th>
                  <th>Tanggal Inspeksi</th>
                  <th>Status</th>
                  <th>Disetujui Oleh</th>
                  <th width="15%" class="text-center">Aksi</th>

               </tr>

            </thead>

            <tbody>

               @forelse($dataSs6 as $item)

                  <tr>

                     <td class="text-center">
                        {{ $loop->iteration }}
                     </td>

                     <td>{{ $item->no_asset }}</td>

                     <td>{{ $item->no_lambung }}</td>

                     <td>{{ $item->serial_number }}</td>

                     <td>
                        {{ \Carbon\Carbon::parse($item->tanggal_inspeksi)->format('d M Y') }}
                     </td>

                     <td><span class="badge {{ $item->approved_at ? 'text-bg-success' : 'text-bg-warning' }}">{{ $item->approved_at ? 'Approved' : 'Menunggu GL' }}</span></td>

                     <td>{{ $item->approved_by ?: '-' }}</td>

                     <td class="text-center">

                        <div class="btn-group gap-1">
                           @if ($item->photo_path)
                              <button type="button" class="btn btn-info btn-sm text-white" data-bs-toggle="modal"
                                 data-bs-target="#photoPreviewModal" data-photo-url="{{ asset('storage/' . $item->photo_path) }}"
                                 data-photo-name="{{ $item->no_asset ?: 'SS6' }}" title="Lihat Foto"><i class="bi bi-image"></i></button>
                           @endif
                           <a href="{{ route('inspeksi.ss6.pdf', $item) }}" target="_blank" class="btn btn-danger btn-sm"><i class="bi bi-file-pdf"></i> PDF</a>
                           @if ($item->approved_at)
                           @else
                              <a href="{{ route('inspeksi.ss6.edit', $item) }}" class="btn btn-warning btn-sm text-white"><i class="bi bi-pencil"></i></a>
                              @if ($isGroupLeader)
                              <form action="{{ route('inspeksi.ss6.approve', $item) }}" method="POST">@csrf<button class="btn btn-success btn-sm" onclick="return confirm('Approve inspeksi ini?')"><i class="bi bi-check-lg"></i></button></form>
                              @endif
                           @endif
                           <form action="{{ route('inspeksi.ss6.destroy', $item) }}" method="POST">@csrf @method('DELETE')<button class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data?')"><i class="bi bi-trash"></i></button></form>

                        </div>

                     </td>

                  </tr>

               @empty

                  <tr>

                     <td colspan="8" class="text-center py-4">

                        Belum ada data inspeksi.

                     </td>

                  </tr>

               @endforelse

            </tbody>

         </table>

      </div>

   </div>

   <div class="mt-3">

      {{ $dataSs6->links() }}

   </div>
   @if (Auth()->user()->nrp == 250504)
      <x-clone-modal route="{{ route('inspeksi.ss6.clone') }}"></x-clone-modal>
   @endif
   <x-photo-modal />

@endsection
