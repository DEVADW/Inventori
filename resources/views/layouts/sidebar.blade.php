@php
    $userJabatan = strtolower(trim((string) (Auth::user()->jabatan ?? '')));
    $isIctTechnician = in_array($userJabatan, ['ict_technician', 'ict technician'], true);
    $isHardwareEngineer = in_array($userJabatan, ['hardware engineer', 'hardware_enggineer', 'hardware engg', 'hardware_engg'], true);
@endphp

<aside class="app-sidebar shadow" data-bs-theme="dark">
    <div>
        <div class="sidebar-brand">
            <a href="#" class="brand-link">
                <img src="{{ asset('asset/images/logos/logo_ppa.png') }}" alt="AdminLTE Logo"
                    class="brand-image img-white-border opacity-100 shadow" />
                <span class="brand-text fw-bold">PUTRA PERKASA ABADI</span>
            </a>
        </div>
        <div class="sidebar-wrapper">
            <nav class="mt-2">
                <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">

                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}"
                            class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-speedometer"></i>
                            <p class="sidebar-link">Dashboard</p>
                        </a>
                    </li>

                    <!-- Buku Tamu - Admin & Security Only -->
                    @can('BukuTamu')
                        <li class="nav-item">
                            <a href="{{ route('tamu.index') }}"
                                class="nav-link {{ request()->routeIs('tamu.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-book"></i>
                                <p class="sidebar-link">Buku Tamu</p>
                            </a>
                        </li>
                    @endcan

                    <!-- Inventaris - Admin Only -->
                    @if(Auth::user()->can('isIct') || Auth::user()->can('isGL') || Auth::user()->can('isAdmin'))
                        <li class="nav-item">
                            <a href="{{ route('inventaris.index') }}"
                                class="nav-link {{ request()->routeIs('inventaris.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-box-seam"></i>
                                <p class="sidebar-link">Inventaris</p>
                            </a>
                        </li>
                    @endif

                    <!-- Menu Dropdown Data Aset - Jabatan ICT, GL, dan Admin -->
                    @if(Auth::user()->can('isIct') || Auth::user()->can('isGL') || Auth::user()->can('isAdmin'))
                        <li class="nav-item {{ request()->routeIs('aset.*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->routeIs('aset.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-hdd-stack"></i>
                                <p>
                                    Data Aset
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('aset.simcard.index') }}"
                                        class="nav-link {{ request()->routeIs('aset.simcard.*') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Data SIM Card</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('aset.unitoperasi.index') }}"
                                        class="nav-link {{ request()->routeIs('aset.unitoperasi.*') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Unit Operasi</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('aset.inventoristavolt.index') }}"
                                        class="nav-link {{ request()->routeIs('aset.inventoristavolt.*') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Inventori Stavolt</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('aset.inventoriups.index') }}"
                                        class="nav-link {{ request()->routeIs('aset.inventoriups.*') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Inventori UPS</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('aset.inventoritv.index') }}"
                                        class="nav-link {{ request()->routeIs('aset.inventoritv.*') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Inventori TV</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('aset.inventoriproyektor.index') }}"
                                        class="nav-link {{ request()->routeIs('aset.inventoriproyektor.*') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Inventori Proyektor</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

@php
    $allowedInspectionModules = Auth::user()->allowedInspectionModules();
    $inspectionModuleDefinitions = \App\Models\User::inspectionModules();
@endphp

                    @if(!empty($allowedInspectionModules))
                        <li class="nav-item {{ request()->routeIs('inspeksi.*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->routeIs('inspeksi.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-tools"></i>
                                <p>
                                    Inspeksi
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                @foreach ($allowedInspectionModules as $moduleKey)
                                    @if (isset($inspectionModuleDefinitions[$moduleKey]))
                                        <li class="nav-item">
                                            <a href="{{ route($inspectionModuleDefinitions[$moduleKey]['route']) }}"
                                                class="nav-link {{ request()->routeIs('inspeksi.' . $moduleKey . '.*') ? 'active' : '' }}">
                                                <i class="nav-icon bi bi-circle"></i>
                                                <p>{{ $inspectionModuleDefinitions[$moduleKey]['label'] }}</p>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </li>
                    @endif

                    <!-- Admin Menu Section -->
                   @if(Auth::user()->can('isGL') || Auth::user()->can('isAdmin'))
                        @can('isAdmin')
                            <li class="nav-item">
                                <a href="{{ route('manajemen-akun.index') }}"
                                    class="nav-link {{ request()->routeIs('manajemen-akun.*') ? 'active' : '' }}">
                                    <i class="bi bi-key"></i>
                                    <p class="sidebar-link">Manajemen Akun</p>
                                </a>
                            </li>
                        @endcan

                        <li class="nav-item">
                            <a href="{{ route('users.index') }}"
                                class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                <i class="bi bi-people"></i>
                                <p class="sidebar-link">Manajemen Pengguna</p>
                            </a>
                        </li>

                        @can('isAdmin')
                            <li class="nav-item">
                                <a href="{{ route('settings.inspeksi.index') }}"
                                    class="nav-link {{ request()->routeIs('settings.inspeksi.*') ? 'active' : '' }}">
                                    <i class="bi bi-sliders"></i>
                                    <p class="sidebar-link">Pengaturan Inspeksi</p>
                                </a>
                            </li>
                        @endcan

                    @endif

                    <!-- Account Settings -->
                    <li class="nav-item mt-3 pt-2 border-top">
                        <a href="{{ route('account-settings.index') }}"
                            class="nav-link {{ request()->routeIs('account-settings.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-gear"></i>
                            <p class="sidebar-link">Pengaturan Akun</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</aside>
