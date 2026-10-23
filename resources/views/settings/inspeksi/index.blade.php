@extends('layouts.app')
@section('judul', 'Pengaturan Periode Inspeksi')
@section('subjudul', 'Atur Range Inspeksi Per Modul')

@section('konten')
    <div class="row g-3">
        @foreach ($settings as $setting)
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h6 class="fw-bold mb-1">{{ $setting['label'] }}</h6>
                        <p class="text-muted small mb-3">Periode saat ini: {{ $setting['period_label'] }}</p>

                        <form action="{{ route('settings.inspeksi.update', $setting['module']) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <label for="interval_months_{{ $setting['module'] }}" class="form-label">Interval Inspeksi
                                (bulan)</label>
                            <div class="input-group">
                                <input type="number" min="1" max="24" id="interval_months_{{ $setting['module'] }}"
                                    name="interval_months" class="form-control"
                                    value="{{ $setting['interval_months'] }}">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                            <div class="form-text">Contoh: 1 = tiap bulan, 3 = tiap 3 bulan.</div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
