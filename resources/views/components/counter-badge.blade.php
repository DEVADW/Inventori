@props(['title', 'counter', 'bgColor'])

<div>
    <!-- Act only according to that maxim whereby you can, at the same time, will that it should become a universal law. - Immanuel Kant -->

    <div class="card {{ $bgColor }} mb-0" style="width: 12rem;">
        <div class="card-body p-2 text-center">
            <h6 class="card-title mb-1 small fw-bold text-uppercase">{{ $title }}</h6>
            <h4 class="card-text fw-bold mb-0">{{ $counter }}</h4>
        </div>
    </div>
</div>
