@include('layouts.header')
@include('layouts.sidebar')
<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <h3 class="mb-0">@yield('judul')</h3>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header text-white" style="background-color: #d62828;">
                    <h5 class="card-title">@yield('subjudul')</h5>
                </div>
                <div class="card-body">
                    @yield('konten')
                </div>
            </div>
        </div>
    </div>
</main>
@stack('scripts')
@include('layouts.footer')
