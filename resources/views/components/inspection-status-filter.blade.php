@props(['route', 'status' => null, 'totalAset', 'totalSudah', 'totalBelum'])

@php
    $baseQuery = request()->except(['status', 'page']);
    $linkFor = fn ($value) => route($route, $value ? array_merge($baseQuery, ['status' => $value]) : $baseQuery);
    $isActive = fn ($value) => $status === $value;

    $cards = [
        ['value' => null, 'title' => 'Total Unit', 'counter' => $totalAset, 'bg' => 'bg-info-subtle'],
        ['value' => 'sudah', 'title' => 'Sudah Diinspeksi', 'counter' => $totalSudah, 'bg' => 'bg-success-subtle'],
        ['value' => 'belum', 'title' => 'Belum Diinspeksi', 'counter' => $totalBelum, 'bg' => 'bg-warning-subtle'],
    ];
@endphp

@foreach ($cards as $card)
    <a href="{{ $linkFor($card['value']) }}" class="text-decoration-none text-reset" title="Klik untuk filter">
        <div class="card {{ $card['bg'] }} mb-0 {{ $isActive($card['value']) ? 'border border-3 border-dark-subtle shadow-sm' : '' }}"
            style="width: 12rem;">
            <div class="card-body p-2 text-center">
                <h6 class="card-title mb-1 small fw-bold text-uppercase">{{ $card['title'] }}</h6>
                <h4 class="card-text fw-bold mb-0">{{ $card['counter'] }}</h4>
            </div>
        </div>
    </a>
@endforeach
