@extends('layouts.app')
@section('judul', 'Dashboard')
@section('subjudul', 'Ringkasan Operasional')
@section('konten')
@php
    $isHardwareEngineer = $isHardwareEngineer ?? false;
    $isIctTechnician = $isIctTechnician ?? false;
@endphp
    <style>
        .dashboard-hero {
            background: linear-gradient(125deg, #8f1515, #d62828);
            border-radius: 1rem;
            color: #fff;
        }

        .dashboard-stat {
            border: 0;
            border-radius: 1rem;
            height: 100%;
            box-shadow: 0 .25rem 1rem rgba(0, 0, 0, .07);
        }

        .stat-icon {
            width: 3rem;
            height: 3rem;
            display: grid;
            place-items: center;
            border-radius: .85rem;
            font-size: 1.35rem;
        }

        .quick-link {
            border: 1px solid var(--bs-border-color);
            border-radius: .75rem;
            color: var(--bs-body-color);
            text-decoration: none;
            transition: .2s ease;
        }

        .quick-link:hover {
            border-color: #d62828;
            color: #d62828;
            transform: translateY(-2px);
        }

        .dashboard-list-icon {
            width: 2.25rem;
            height: 2.25rem;
            display: grid;
            place-items: center;
            border-radius: 50%;
        }
    </style>


    <div class="dashboard-hero p-4 p-lg-5 mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <p class="text-white-50 mb-1">{{ now()->translatedFormat('l, d F Y') }}</p>
                <h2 class="fw-bold mb-2">Selamat datang, {{ Auth::user()->nama }}!</h2>
                <p class="mb-0 text-white-50">
                    @if($isHardwareEngineer)
                        Pantau inspeksi OFA, lihat jumlah inspeksi pada bulan ini, dan cek siapa yang terakhir melakukan inspeksi.
                    @elseif($isIctTechnician)
                        Pantau inspeksi ICC, lihat jumlah inspeksi pada bulan ini, dan cek siapa yang terakhir melakukan inspeksi.
                    @elseif(Auth::user()->role === 'admin')
                        Pantau peminjaman perangkat, kunjungan tamu, dan inspeksi dalam satu tempat.
                    @elseif(Auth::user()->jabatan === 'security')
                        Pantau kunjungan tamu dalam satu tempat.
                    @else
                        Pantau dan catat inspeksi perangkat dalam satu tempat.
                    @endif
                </p>
            </div>
            @can('isAdmin')
                <a href="{{ route('inventaris.create') }}" class="btn btn-light text-danger fw-semibold px-3"><i
                        class="bi bi-plus-lg me-1"></i> Tambah Peminjaman</a>
            @endcan

        </div>
    </div>

    @if($isHardwareEngineer)
        <div class="row g-4 mb-4">
            <div class="col-lg-4">
                <div class="card dashboard-stat">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-body-secondary">Total Data Inspeksi</small>
                                <div class="fs-3 fw-bold text-danger">{{ $totalOfaData }}</div>
                            </div>
                            <span class="stat-icon bg-danger-subtle text-danger"><i class="bi bi-clipboard-check"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card dashboard-stat">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-body-secondary">Inspeksi Bulan Ini</small>
                                <div class="fs-3 fw-bold text-danger">{{ $ofaThisMonth }}</div>
                            </div>
                            <span class="stat-icon bg-success-subtle text-success"><i class="bi bi-calendar-event"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card dashboard-stat">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-body-secondary">Akses Cepat</small>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <a href="{{ route('inspeksi.ofa.create') }}" class="btn btn-sm btn-danger">
                                        <i class="bi bi-clipboard-plus me-1"></i>Isi Inspeksi
                                    </a>
                                    <a href="{{ route('inspeksi.ofa.index') }}" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-list-ul me-1"></i>Buka Index
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-12">
                <div class="card dashboard-stat">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h5 class="mb-1">Inspeksi terbaru</h5>
                                <small class="text-body-secondary">Siapa yang melakukan inspeksi terakhir</small>
                            </div>
                        </div>
                        @forelse($latestOfaInspectors as $latest)
                            @php
                                $names = collect($latest->tim_pelaksana ?? [])->pluck('nama')->filter()->implode(', ');
                                $names = $names ?: ($latest->diinspeksi_oleh ?: 'Petugas belum diisi');
                            @endphp
                            <div class="d-flex align-items-center gap-2 py-2 border-top">
                                <span class="dashboard-list-icon bg-warning-subtle text-warning"><i class="bi bi-person-check"></i></span>
                                <div class="text-truncate">
                                    <div class="fw-semibold text-truncate">{{ $names }}</div>
                                    <small class="text-body-secondary">
                                        {{ $latest->code_number_unit ?: '-' }} • {{ $latest->tanggal_inspeksi?->translatedFormat('d F Y') ?? '-' }}
                                    </small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-body-secondary py-4"><i class="bi bi-person-x fs-3 d-block mb-2"></i>Belum ada inspeksi OFA.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @elseif($isIctTechnician)
        <div class="row g-4 mb-4">
            <div class="col-lg-4">
                <div class="card dashboard-stat">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-body-secondary">Total Data Inspeksi</small>
                                <div class="fs-3 fw-bold text-danger">{{ $totalIccData }}</div>
                            </div>
                            <span class="stat-icon bg-danger-subtle text-danger"><i class="bi bi-clipboard-check"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card dashboard-stat">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-body-secondary">Inspeksi Bulan Ini</small>
                                <div class="fs-3 fw-bold text-danger">{{ $iccThisMonth }}</div>
                            </div>
                            <span class="stat-icon bg-success-subtle text-success"><i class="bi bi-calendar-event"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card dashboard-stat">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-body-secondary">Akses Cepat</small>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <a href="{{ route('inspeksi.icc.index') }}" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-list-ul me-1"></i>Buka Index
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-12">
                <div class="card dashboard-stat">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h5 class="mb-1">Inspeksi terbaru</h5>
                                <small class="text-body-secondary">Siapa yang melakukan inspeksi terakhir</small>
                            </div>
                        </div>
                        @forelse($latestIccInspectors as $latest)
                            <div class="d-flex align-items-center gap-2 py-2 border-top">
                                <span class="dashboard-list-icon bg-warning-subtle text-warning"><i class="bi bi-person-check"></i></span>
                                <div class="text-truncate">
                                    <div class="fw-semibold text-truncate">{{ $latest->inspektor ?: ($latest->diperiksa_oleh ?: 'Petugas belum diisi') }}</div>
                                    <small class="text-body-secondary">
                                        {{ $latest->no_lambung_unit ?: '-' }} • {{ $latest->tanggal_inspeksi?->translatedFormat('d F Y') ?? '-' }}
                                    </small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-body-secondary py-4"><i class="bi bi-person-x fs-3 d-block mb-2"></i>Belum ada inspeksi ICC.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif



    <div class="row g-3 mb-4">
        @can('isAdmin')
            <div class="col-sm-6 col-xl-3">
                <div class="card dashboard-stat">
                    <div class="card-body d-flex align-items-center gap-3"><span
                            class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-box-seam"></i></span>
                        <div>
                            <div class="text-body-secondary small">Total Inventaris</div>
                            <div class="fs-3 fw-bold">{{ $totalInventaris }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card dashboard-stat">
                    <div class="card-body d-flex align-items-center gap-3"><span
                            class="stat-icon bg-warning-subtle text-warning-emphasis"><i class="bi bi-arrow-repeat"></i></span>
                        <div>
                            <div class="text-body-secondary small">Masih Dipinjam</div>
                            <div class="fs-3 fw-bold">{{ $dipinjam }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
        @can('BukuTamu')
            <div class="col-sm-6 col-xl-3">
                <div class="card dashboard-stat">
                    <div class="card-body d-flex align-items-center gap-3"><span
                            class="stat-icon bg-success-subtle text-success"><i class="bi bi-person-check"></i></span>
                        <div>
                            <div class="text-body-secondary small">Tamu Hari Ini</div>
                            <div class="fs-3 fw-bold">{{ $totalTamuHariIni }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
        @if(Auth::user()->role === 'admin' || Auth::user()->can('isGL') || Auth::user()->can('isIct'))
            <div class="col-sm-6 col-xl-3">
                <div class="card dashboard-stat">
                    <div class="card-body d-flex align-items-center gap-3"><span
                            class="stat-icon bg-danger-subtle text-danger"><i class="bi bi-clipboard2-check"></i></span>
                        <div>
                            <div class="text-body-secondary small">Total Inspeksi</div>
                            <div class="fs-3 fw-bold">{{ $totalInspeksi }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            @can('isAdmin')
                <div class="card dashboard-stat">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h5 class="mb-1">Peminjaman terbaru</h5><small class="text-body-secondary">Perangkat yang baru
                                    dicatat</small>
                            </div><a href="{{ route('inventaris.index') }}" class="btn btn-sm btn-outline-danger">Lihat
                                semua</a>
                        </div>
                        @forelse ($inventarisTerbaru as $item)
                            <div class="d-flex align-items-center justify-content-between py-2 border-top">
                                <div class="d-flex align-items-center gap-2"><span
                                        class="dashboard-list-icon bg-primary-subtle text-primary"><i
                                            class="bi bi-laptop"></i></span>
                                    <div>
                                        <div class="fw-semibold">{{ $item->nama_perangkat }}</div><small
                                            class="text-body-secondary">{{ $item->nama }}{{ $item->no_asset ? ' · ' . $item->no_asset : '' }}</small>
                                    </div>
                                </div><span
                                    class="badge {{ $item->status_peminjaman === 'Dikembalikan' ? 'text-bg-success' : 'text-bg-warning' }}">{{ $item->status_peminjaman }}</span>
                            </div>
                        @empty
                            <div class="text-center text-body-secondary py-4"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum
                                ada data peminjaman.</div>
                        @endforelse
                    </div>
                </div>
            @else
                @can('BukuTamu')
                    <div class="card dashboard-stat">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <h5 class="mb-1">Kunjungan terbaru</h5><small class="text-body-secondary">Catatan dari buku
                                        tamu</small>
                                </div><a href="{{ route('tamu.index') }}" class="btn btn-sm btn-outline-danger">Lihat semua</a>
                            </div>
                            @forelse ($tamuTerbaru as $tamu)
                                <div class="d-flex align-items-center gap-2 py-2 border-top"><span
                                        class="dashboard-list-icon bg-success-subtle text-success"><i
                                            class="bi bi-person"></i></span>
                                    <div class="text-truncate">
                                        <div class="fw-semibold text-truncate">{{ $tamu->nama }}</div><small
                                            class="text-body-secondary">{{ $tamu->instansi ?: 'Instansi tidak diisi' }}</small>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-body-secondary py-4"><i
                                        class="bi bi-person-x fs-3 d-block mb-2"></i>Belum ada kunjungan tercatat.</div>
                            @endforelse
                        </div>
                    </div>
                @endcan
            @endif
        </div>
        <div class="col-lg-5">
            @can('isAdmin')
                <div class="card dashboard-stat">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h5 class="mb-1">Kunjungan terbaru</h5><small class="text-body-secondary">Catatan dari buku
                                    tamu</small>
                            </div><a href="{{ route('tamu.index') }}" class="btn btn-sm btn-outline-danger">Lihat semua</a>
                        </div>
                        @forelse ($tamuTerbaru as $tamu)
                            <div class="d-flex align-items-center gap-2 py-2 border-top"><span
                                    class="dashboard-list-icon bg-success-subtle text-success"><i
                                        class="bi bi-person"></i></span>
                                <div class="text-truncate">
                                    <div class="fw-semibold text-truncate">{{ $tamu->nama }}</div><small
                                        class="text-body-secondary">{{ $tamu->instansi ?: 'Instansi tidak diisi' }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-body-secondary py-4"><i
                                    class="bi bi-person-x fs-3 d-block mb-2"></i>Belum ada kunjungan tercatat.</div>
                        @endforelse
                    </div>
                </div>
            @endcan
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            @if(Auth::user()->role === 'admin' || Auth::user()->can('isGL') || Auth::user()->can('isIct') || Auth::user()->can('isIctTechnician'))
                <div class="card dashboard-stat">
                    <div class="card-body p-4">
                        <h5 class="mb-1">Ringkasan inspeksi</h5>
                        <p class="text-body-secondary small mb-3">Jumlah formulir inspeksi yang sudah tercatat.</p>
                        <div class="row g-2 text-center">
                            <div class="col-6 col-md-3">
                                <div class="bg-body-tertiary rounded-3 p-3">
                                    <div class="fs-4 fw-bold">{{ $inspeksi['stavolt'] }}</div><small>Stavolt</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="bg-body-tertiary rounded-3 p-3">
                                    <div class="fs-4 fw-bold">{{ $inspeksi['ups'] }}</div><small>UPS</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="bg-body-tertiary rounded-3 p-3">
                                    <div class="fs-4 fw-bold">{{ $inspeksi['monitor'] }}</div><small>Monitor/TV</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="bg-body-tertiary rounded-3 p-3">
                                    <div class="fs-4 fw-bold">{{ $inspeksi['proyektor'] }}</div><small>Proyektor</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="col-lg-5">
            <div class="card dashboard-stat">
                <div class="card-body p-4">
                    <h5 class="mb-3">Akses cepat</h5>
                    <div class="row g-2">
                        @can('isAdmin')
                            <div class="col-6"><a class="quick-link d-block p-3" href="{{ route('tamu.create') }}"><i
                                        class="bi bi-person-plus d-block fs-5 mb-1"></i><small class="fw-semibold">Tambah
                                        tamu</small></a></div>
                        @endcan
                        @if(Auth::user()->jabatan === 'security')
                            <div class="col-6"><a class="quick-link d-block p-3" href="{{ route('tamu.index') }}"><i
                                        class="bi bi-book d-block fs-5 mb-1"></i><small class="fw-semibold">Buku Tamu</small></a></div>
                        @elseif(Auth::user()->role === 'admin' || Auth::user()->can('isGL'))
                            <div class="col-6"><a class="quick-link d-block p-3"
                                    href="{{ route('inspeksi.ups.create') }}"><i
                                        class="bi bi-clipboard-plus d-block fs-5 mb-1"></i><small class="fw-semibold">Inspeksi
                                        UPS</small></a></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
