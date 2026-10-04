@props(['action', 'placeholder' => 'Cari data...', 'value' => request('search')])

<div>
    <!-- Very little is needed to make a happy life. - Marcus Aurelius -->

    <form action="{{ $action }}" method="GET" class="d-flex gap-2">
        <div class="input-group input-group-sm" style="width: 16rem">
            <span class="input-group-text">
                <i class="bi bi-search" aria-hidden="true"></i>
                </span>
            <input id="table-filter" type="search" name="search" class="form-control" placeholder="{{ $placeholder }}"
                    aria-label="Filter rows" value="{{ $value }}"/>
            <button class="btn btn-outline-primary" type="submit">
                Cari
            </button>

            {{-- Tombol reset jika sedang melakukan pencarian --}}
            @if ($value)
                <a href="{{ $action }}" class="btn btn-outline-secondary">Reset</a>
            @endif
        </div>
    </form>
</div>