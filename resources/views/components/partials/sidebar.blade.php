<aside class="drawer-side">
    <label for="sidebar-drawer" class="drawer-overlay"></label>

    <div class="bg-base-100 text-base-content h-full w-64 md:w-72 border-r border-base-300 flex flex-col">
        <!-- Sidebar Header - Fixed at Top -->
        <div class="flex items-center gap-3 border-b border-base-300 bg-base-100 sticky top-0 z-10 navbar">
            <div class="bg-primary text-primary-content p-2 rounded-xl">
                <x-heroicon-o-presentation-chart-line class="w-5" />
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-xl font-bold truncate">{{ config('app.name', 'Absensi App') }}</h2>
                <p class="text-xs text-gray-500">v1.0.0</p>
            </div>
        </div>

        <!-- Scrollable Content Area -->
        <div class="flex-1 overflow-y-auto scrollbar-hide p-1">
            <!-- User Info -->
            {{-- <div class="bg-base-200 rounded-xl p-4 mb-6">
                <div class="flex items-center">
                    <div class="avatar placeholder mr-3">
                        <div class="bg-primary text-primary-content rounded-full w-12">
                            @if (Auth::user()->avatar)
                                <img src="{{ Auth::user()->avatar }}" alt="User Avatar" />
                            @else
                                <span class="text-lg font-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <p class="font-bold truncate">{{ Auth::user()->name }}</p>
                        <p class="text-sm text-gray-500">
                            <span
                                class="badge badge-sm 
                                {{ Auth::user()->role === 'admin' ? 'badge-primary' : 'badge-secondary' }}">
                                {{ ucfirst(Auth::user()->role) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div> --}}

            <!-- Menu Navigation -->
            <ul class="menu sidebar-menu space-y-1 w-full">
                <!-- ADMIN MENU -->
                <li class="menu-title">
                    <span>Administrator</span>
                </li>

                <li>
                    <a wire:navigate href="{{ route('admin.dashboard.index') }}"
                        class="{{ request()->routeIs('admin.dashboard.*') ? 'bg-base-300' : '' }}">
                        {{-- <x-hugeicons-dashboard-circle class="w-5" /> --}}
                        <x-heroicon-o-home class="w-5" />
                        Dashboard
                    </a>
                </li>

                <li>
                    <a wire:navigate href="{{ route('admin.karyawan.index') }}"
                        class="{{ request()->routeIs('admin.karyawan.*') ? 'bg-base-300' : '' }}">
                        <x-clarity-employee-group-line class="w-5" />
                        Data Karyawan
                    </a>
                </li>

                <li>
                    <a wire:navigate href="{{ route('admin.wajah-karyawan.index') }}"
                        class="{{ request()->routeIs('admin.wajah-karyawan.*') ? 'bg-base-300' : '' }}">
                        <x-heroicon-o-face-smile class="w-5" />
                        Wajah Karyawan
                    </a>
                </li>

                <li>
                    <a wire:navigate href="{{ route('admin.lokasi.index') }}"
                        class="{{ request()->routeIs('admin.lokasi.*') ? 'bg-base-300' : '' }}">
                        <x-heroicon-o-map class="w-5" />
                        Data Lokasi
                    </a>
                </li>

                <li>
                    <a wire:navigate href="{{ route('admin.shift.index') }}"
                        class="{{ request()->routeIs('admin.shift.*') ? 'bg-base-300' : '' }}">
                        <x-heroicon-o-clock class="w-5" />
                        Data Shift
                    </a>
                </li>

                <li>
                    <a wire:navigate href="{{ route('admin.jadwal.index') }}"
                        class="{{ request()->routeIs('admin.jadwal.*') ? 'bg-base-300' : '' }}">
                        <x-heroicon-o-calendar class="w-5" />
                        Jadwal Kerja
                    </a>
                </li>

                <li>
                    <a wire:navigate href="{{ route('admin.absensi.index') }}"
                        class="{{ request()->routeIs('admin.absensi.*') ? 'bg-base-300' : '' }}">
                        <x-heroicon-o-chart-bar class="w-5" />
                        Rekap Absensi
                    </a>
                </li>

                <li>
                    <details {{ request()->routeIs('admin.departemen.*', 'admin.jabatan.*') ? 'open' : '' }}>
                        <summary>
                            <x-heroicon-o-building-office class="w-5" />
                            Posisi
                        </summary>
                        <ul>
                            <li>
                                <a wire:navigate href="{{ route('admin.departemen.index') }}"
                                    class="{{ request()->routeIs('admin.departemen.*') ? 'bg-base-300' : '' }}">
                                    Departemen
                                </a>
                            </li>
                            <li>
                                <a wire:navigate href="{{ route('admin.jabatan.index') }}"
                                    class="{{ request()->routeIs('admin.jabatan.*') ? 'bg-base-300' : '' }}">
                                    Jabatan
                                </a>
                            </li>
                        </ul>
                    </details>
                </li>

                <li wire:poll.1s>
                    <details {{ request()->routeIs('admin.izin.*', 'admin.cuti.*', 'admin.saldo.*') ? 'open' : '' }}>
                        <summary>
                            {{-- <x-heroicon-o-document-text class="w-5" /> --}}
                            <x-iconpark-permissions-o class="w-5" />
                            Pengajuan
                            @if($totalPendingCount > 0)
                                <span class="badge badge-warning badge-soft badge-xs">{{ $totalPendingCount }}</span>
                            @endif
                        </summary>
                        <ul>
                            <li>
                                <a wire:navigate href="{{ route('admin.izin.index') }}"
                                    class="{{ request()->routeIs('admin.izin.*') ? 'bg-base-300' : '' }}">
                                    Izin
                                    @if($pendingIzinCount > 0)
                                        <span class="badge badge-info badge-soft badge-xs">{{ $pendingIzinCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a wire:navigate href="{{ route('admin.cuti.index') }}"
                                    class="{{ request()->routeIs('admin.cuti.*') ? 'bg-base-300' : '' }}">
                                    Cuti
                                    @if($pendingCutiCount > 0)
                                        <span class="badge badge-secondary badge-soft badge-xs">{{ $pendingCutiCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a wire:navigate href="{{ route('admin.saldo.index') }}"
                                    class="{{ request()->routeIs('admin.saldo.*') ? 'bg-base-300' : '' }}">
                                    Saldo Cuti & Izin
                                </a>
                            </li>
                        </ul>
                    </details>
                </li>

                <li class="menu-title mt-6">
                    <span>Sistem</span>
                </li>

                <li>
                    <a wire:navigate href="/"
                        class="{{ request()->routeIs('admin.pengaturan.*') ? 'bg-base-300' : '' }}">
                        <x-heroicon-o-cog-6-tooth class="w-5" />
                        Pengaturan Sistem
                    </a>
                </li>

                <li>
                    <a wire:navigate href="{{ route('admin.laporan.index') }}"
                        class="{{ request()->routeIs('admin.laporan.*') ? 'bg-base-300' : '' }}">
                        <x-heroicon-o-document-chart-bar class="w-5" />
                        Laporan
                    </a>
                </li>

                <!-- Common Menu Items -->
                <li class="menu-title mt-6">
                    <span>Umum</span>
                </li>

                <li>
                    <a wire:navigate href="/">
                        <x-heroicon-o-question-mark-circle class="w-5" />
                        Bantuan
                    </a>
                </li>

                <li>
                    <a wire:navigate href="/">
                        <x-heroicon-o-shield-check class="w-5" />
                        Kebijakan Privasi
                    </a>
                </li>
            </ul>
        </div>

        <!-- Sidebar Footer -->
        {{-- <div class="absolute bottom-4 left-4 right-4">
            <div class="stats shadow bg-base-200">
                <div class="stat p-3">
                    <div class="stat-title text-xs">Status Sistem</div>
                    <div class="stat-value text-sm text-success">Online</div>
                    <div class="stat-desc text-xs">Last sync: {{ now()->format('H:i') }}</div>
                </div>
            </div>
        </div> --}}
    </div>
</aside>
