@props(['field', 'label'])

@php
    $currentSort = request('sort');
    $currentDirection = request('direction');
    $isActive = $currentSort === $field;

    if (!$isActive) {
        $nextSort = $field;
        $nextDirection = 'asc';
    } elseif ($currentDirection === 'asc') {
        $nextSort = $field;
        $nextDirection = 'desc';
    } else {
        // Sudah descending -> kembali ke urutan default (berdasarkan id)
        $nextSort = null;
        $nextDirection = null;
    }

    $queryParams = request()->except(['sort', 'direction', 'page']);
    if ($nextSort) {
        $queryParams['sort'] = $nextSort;
        $queryParams['direction'] = $nextDirection;
    }
@endphp

<th {{ $attributes }}>
    <a href="{{ request()->url() }}?{{ http_build_query($queryParams) }}"
        class="text-decoration-none text-dark d-inline-flex align-items-center gap-1">
        {{ $label }}
        @if ($isActive && $currentDirection === 'asc')
            <i class="bi bi-sort-up small"></i>
        @elseif ($isActive && $currentDirection === 'desc')
            <i class="bi bi-sort-down small"></i>
        @else
            <i class="bi bi-arrow-down-up text-muted small"></i>
        @endif
    </a>
</th>
